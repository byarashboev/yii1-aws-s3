<?php

namespace byarashboev\aws\s3\tests\handlers;

use Aws\CommandInterface as AwsCommandInterface;
use Aws\ResultInterface;
use Aws\S3\S3Client;
use byarashboev\aws\s3\handlers\PlainCommandHandler;
use byarashboev\aws\s3\interfaces\Bus;
use byarashboev\aws\s3\interfaces\commands\Asynchronous;
use byarashboev\aws\s3\interfaces\commands\Command;
use byarashboev\aws\s3\interfaces\commands\PlainCommand;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;

/**
 * A concrete PlainCommand implementation for testing.
 */
class TestPlainCommand implements PlainCommand
{
    private string $name;
    private array $args;

    public function __construct(string $name = 'GetObject', array $args = [])
    {
        $this->name = $name;
        $this->args = $args;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArgs(): array
    {
        return $this->args;
    }
}

/**
 * A PlainCommand that is also Asynchronous.
 */
class TestAsyncPlainCommand extends TestPlainCommand implements Asynchronous
{
    private bool $isAsync = false;

    public function async(): self
    {
        $this->isAsync = true;
        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }
}

class PlainCommandHandlerTest extends TestCase
{
    public function testHandleSyncWaitsForPromiseAndReturnsResult(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $awsCommand = $this->createMock(AwsCommandInterface::class);
        $result = $this->createMock(ResultInterface::class);

        $promise = new FulfilledPromise($result);

        $command = new TestPlainCommand('GetObject', ['Bucket' => 'bucket', 'Key' => 'file.txt']);

        $s3Client->expects($this->once())
            ->method('getCommand')
            ->with('GetObject', ['Bucket' => 'bucket', 'Key' => 'file.txt'])
            ->willReturn($awsCommand);

        $s3Client->expects($this->once())
            ->method('executeAsync')
            ->with($awsCommand)
            ->willReturn($promise);

        $handler = new PlainCommandHandler($s3Client);
        $actual = $handler->handle($command);

        $this->assertSame($result, $actual);
    }

    public function testHandleAsyncReturnsPromiseWithoutWaiting(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $awsCommand = $this->createMock(AwsCommandInterface::class);
        $result = $this->createMock(ResultInterface::class);

        $promise = new FulfilledPromise($result);

        $command = new TestAsyncPlainCommand('PutObject', ['Bucket' => 'bucket', 'Key' => 'file.txt']);
        $command->async();

        $s3Client->method('getCommand')->willReturn($awsCommand);
        $s3Client->method('executeAsync')->willReturn($promise);

        $handler = new PlainCommandHandler($s3Client);
        $actual = $handler->handle($command);

        $this->assertInstanceOf(PromiseInterface::class, $actual);
    }

    public function testHandleFiltersNullArgsBeforePassingToGetCommand(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $awsCommand = $this->createMock(AwsCommandInterface::class);
        $result = $this->createMock(ResultInterface::class);

        $promise = new FulfilledPromise($result);

        // toArgs() returns an array with a null value that should be filtered out
        $command = new TestPlainCommand('DeleteObject', [
            'Bucket' => 'bucket',
            'Key' => 'file.txt',
            'VersionId' => null,
        ]);

        $s3Client->expects($this->once())
            ->method('getCommand')
            ->with('DeleteObject', ['Bucket' => 'bucket', 'Key' => 'file.txt'])
            ->willReturn($awsCommand);

        $s3Client->method('executeAsync')->willReturn($promise);

        $handler = new PlainCommandHandler($s3Client);
        $handler->handle($command);
    }

    public function testHandleSyncCommandThatIsNotAsynchronousInterfaceWaitsForResult(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $awsCommand = $this->createMock(AwsCommandInterface::class);
        $result = $this->createMock(ResultInterface::class);

        $promise = new FulfilledPromise($result);

        // TestPlainCommand does not implement Asynchronous so it's always sync
        $command = new TestPlainCommand('ListObjectsV2', ['Bucket' => 'bucket']);

        $s3Client->method('getCommand')->willReturn($awsCommand);
        $s3Client->method('executeAsync')->willReturn($promise);

        $handler = new PlainCommandHandler($s3Client);
        $actual = $handler->handle($command);

        $this->assertSame($result, $actual);
    }
}

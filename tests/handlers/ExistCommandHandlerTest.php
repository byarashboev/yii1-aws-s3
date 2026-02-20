<?php

namespace byarashboev\aws\s3\tests\handlers;

use Aws\S3\S3Client;
use byarashboev\aws\s3\commands\ExistCommand;
use byarashboev\aws\s3\handlers\ExistCommandHandler;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class ExistCommandHandlerTest extends TestCase
{
    public function testHandleCallsDoesObjectExistWithBucketFilenameAndOptions(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new ExistCommand($bus);
        $command->inBucket('my-bucket')->byFilename('path/to/file.txt');
        $command->withOptions(['@http' => ['connect_timeout' => 5]]);

        $s3Client->expects($this->once())
            ->method('doesObjectExist')
            ->with('my-bucket', 'path/to/file.txt', ['@http' => ['connect_timeout' => 5]])
            ->willReturn(true);

        $handler = new ExistCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertTrue($result);
    }

    public function testHandleReturnsTrueWhenObjectExists(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new ExistCommand($bus);
        $command->inBucket('bucket')->byFilename('file.txt');

        $s3Client->method('doesObjectExist')->willReturn(true);

        $handler = new ExistCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertTrue($result);
    }

    public function testHandleReturnsFalseWhenObjectDoesNotExist(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new ExistCommand($bus);
        $command->inBucket('bucket')->byFilename('missing.txt');

        $s3Client->method('doesObjectExist')->willReturn(false);

        $handler = new ExistCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertFalse($result);
    }

    public function testHandlePassesEmptyOptionsWhenNoneSet(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new ExistCommand($bus);
        $command->inBucket('bucket')->byFilename('file.txt');

        $s3Client->expects($this->once())
            ->method('doesObjectExist')
            ->with('bucket', 'file.txt', [])
            ->willReturn(false);

        $handler = new ExistCommandHandler($s3Client);
        $handler->handle($command);
    }
}

<?php

namespace byarashboev\aws\s3\tests;

use Aws\S3\S3Client;
use byarashboev\aws\s3\commands\DeleteCommand;
use byarashboev\aws\s3\commands\ExistCommand;
use byarashboev\aws\s3\commands\GetCommand;
use byarashboev\aws\s3\commands\GetPresignedUrlCommand;
use byarashboev\aws\s3\commands\GetUrlCommand;
use byarashboev\aws\s3\commands\ListCommand;
use byarashboev\aws\s3\commands\PutCommand;
use byarashboev\aws\s3\commands\RestoreCommand;
use byarashboev\aws\s3\commands\UploadCommand;
use byarashboev\aws\s3\HandlerResolver;
use byarashboev\aws\s3\handlers\ExistCommandHandler;
use byarashboev\aws\s3\handlers\GetPresignedUrlCommandHandler;
use byarashboev\aws\s3\handlers\GetUrlCommandHandler;
use byarashboev\aws\s3\handlers\PlainCommandHandler;
use byarashboev\aws\s3\handlers\UploadCommandHandler;
use byarashboev\aws\s3\interfaces\Bus;
use byarashboev\aws\s3\interfaces\commands\Command;
use byarashboev\aws\s3\interfaces\handlers\Handler;
use PHPUnit\Framework\TestCase;

class HandlerResolverTest extends TestCase
{
    private S3Client $s3Client;
    private Bus $bus;

    protected function setUp(): void
    {
        $this->s3Client = $this->createMock(S3Client::class);
        $this->bus = $this->createMock(Bus::class);
    }

    private function makeResolver(): HandlerResolver
    {
        return new HandlerResolver($this->s3Client);
    }

    public function testExistCommandResolvesToExistCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new ExistCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(ExistCommandHandler::class, $handler);
    }

    public function testGetUrlCommandResolvesToGetUrlCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new GetUrlCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(GetUrlCommandHandler::class, $handler);
    }

    public function testGetPresignedUrlCommandResolvesToGetPresignedUrlCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new GetPresignedUrlCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(GetPresignedUrlCommandHandler::class, $handler);
    }

    public function testUploadCommandResolvesToUploadCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new UploadCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(UploadCommandHandler::class, $handler);
    }

    public function testGetCommandResolvesToPlainCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new GetCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(PlainCommandHandler::class, $handler);
    }

    public function testPutCommandResolvesToPlainCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new PutCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(PlainCommandHandler::class, $handler);
    }

    public function testDeleteCommandResolvesToPlainCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new DeleteCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(PlainCommandHandler::class, $handler);
    }

    public function testListCommandResolvesToPlainCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new ListCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(PlainCommandHandler::class, $handler);
    }

    public function testRestoreCommandResolvesToPlainCommandHandler(): void
    {
        $resolver = $this->makeResolver();
        $command = new RestoreCommand($this->bus);

        $handler = $resolver->resolve($command);

        $this->assertInstanceOf(PlainCommandHandler::class, $handler);
    }

    public function testBindHandlerRegistersCustomHandler(): void
    {
        $resolver = $this->makeResolver();
        $customHandler = $this->createMock(Handler::class);

        $resolver->bindHandler(GetCommand::class, $customHandler);

        $command = new GetCommand($this->bus);
        $resolved = $resolver->resolve($command);

        $this->assertSame($customHandler, $resolved);
    }

    public function testBindHandlerRegistersHandlerByClassName(): void
    {
        $resolver = $this->makeResolver();

        $resolver->bindHandler(GetCommand::class, ExistCommandHandler::class);

        $command = new GetCommand($this->bus);
        $resolved = $resolver->resolve($command);

        $this->assertInstanceOf(ExistCommandHandler::class, $resolved);
    }

    public function testUnknownCommandThrowsCException(): void
    {
        $resolver = $this->makeResolver();

        // A command that has no handler and is not a PlainCommand
        $unknownCommand = new class implements Command {};

        $this->expectException(\CException::class);
        $resolver->resolve($unknownCommand);
    }

    public function testSetHandlersBulkRegistersHandlers(): void
    {
        $resolver = $this->makeResolver();

        $customHandler = $this->createMock(Handler::class);

        $resolver->setHandlers([
            GetCommand::class => $customHandler,
            PutCommand::class => $customHandler,
        ]);

        $getCommand = new GetCommand($this->bus);
        $putCommand = new PutCommand($this->bus);

        $this->assertSame($customHandler, $resolver->resolve($getCommand));
        $this->assertSame($customHandler, $resolver->resolve($putCommand));
    }
}

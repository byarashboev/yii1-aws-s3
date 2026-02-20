<?php

namespace byarashboev\aws\s3\tests;

use byarashboev\aws\s3\Bus;
use byarashboev\aws\s3\interfaces\commands\Command;
use byarashboev\aws\s3\interfaces\handlers\Handler;
use byarashboev\aws\s3\interfaces\HandlerResolver;
use PHPUnit\Framework\TestCase;

class BusTest extends TestCase
{
    public function testExecuteCallsResolverAndHandlerAndReturnsResult(): void
    {
        $command = $this->createMock(Command::class);
        $handler = $this->createMock(Handler::class);
        $resolver = $this->createMock(HandlerResolver::class);

        $expectedResult = ['some' => 'result'];

        $resolver->expects($this->once())
            ->method('resolve')
            ->with($command)
            ->willReturn($handler);

        $handler->expects($this->once())
            ->method('handle')
            ->with($command)
            ->willReturn($expectedResult);

        $bus = new Bus($resolver);
        $result = $bus->execute($command);

        $this->assertSame($expectedResult, $result);
    }

    public function testExecutePassesThroughScalarReturnValue(): void
    {
        $command = $this->createMock(Command::class);
        $handler = $this->createMock(Handler::class);
        $resolver = $this->createMock(HandlerResolver::class);

        $resolver->method('resolve')->willReturn($handler);
        $handler->method('handle')->willReturn(true);

        $bus = new Bus($resolver);
        $result = $bus->execute($command);

        $this->assertTrue($result);
    }

    public function testExecutePassesThroughNullReturnValue(): void
    {
        $command = $this->createMock(Command::class);
        $handler = $this->createMock(Handler::class);
        $resolver = $this->createMock(HandlerResolver::class);

        $resolver->method('resolve')->willReturn($handler);
        $handler->method('handle')->willReturn(null);

        $bus = new Bus($resolver);
        $result = $bus->execute($command);

        $this->assertNull($result);
    }

    public function testExecutePassesThroughStringReturnValue(): void
    {
        $command = $this->createMock(Command::class);
        $handler = $this->createMock(Handler::class);
        $resolver = $this->createMock(HandlerResolver::class);

        $resolver->method('resolve')->willReturn($handler);
        $handler->method('handle')->willReturn('https://example.com/file.txt');

        $bus = new Bus($resolver);
        $result = $bus->execute($command);

        $this->assertSame('https://example.com/file.txt', $result);
    }
}

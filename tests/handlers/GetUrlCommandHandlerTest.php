<?php

namespace byarashboev\aws\s3\tests\handlers;

use Aws\S3\S3Client;
use byarashboev\aws\s3\commands\GetUrlCommand;
use byarashboev\aws\s3\handlers\GetUrlCommandHandler;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class GetUrlCommandHandlerTest extends TestCase
{
    public function testHandleCallsGetObjectUrlWithBucketAndFilename(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new GetUrlCommand($bus);
        $command->inBucket('assets-bucket')->byFilename('images/photo.jpg');

        $expectedUrl = 'https://assets-bucket.s3.amazonaws.com/images/photo.jpg';

        $s3Client->expects($this->once())
            ->method('getObjectUrl')
            ->with('assets-bucket', 'images/photo.jpg')
            ->willReturn($expectedUrl);

        $handler = new GetUrlCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertSame($expectedUrl, $result);
    }

    public function testHandleReturnsUrlString(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new GetUrlCommand($bus);
        $command->inBucket('bucket')->byFilename('file.txt');

        $s3Client->method('getObjectUrl')
            ->willReturn('https://bucket.s3.amazonaws.com/file.txt');

        $handler = new GetUrlCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertIsString($result);
        $this->assertSame('https://bucket.s3.amazonaws.com/file.txt', $result);
    }
}

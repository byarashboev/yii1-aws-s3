<?php

namespace byarashboev\aws\s3\tests\handlers;

use Aws\CommandInterface as AwsCommandInterface;
use Aws\S3\S3Client;
use byarashboev\aws\s3\commands\GetPresignedUrlCommand;
use byarashboev\aws\s3\handlers\GetPresignedUrlCommandHandler;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class GetPresignedUrlCommandHandlerTest extends TestCase
{
    public function testHandleCallsGetCommandWithGetObjectAndArgs(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new GetPresignedUrlCommand($bus);
        $command->inBucket('secure-bucket')->byFilename('private/file.pdf')->withExpiration('+1 hour');

        $awsCommand = $this->createMock(AwsCommandInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://presigned-url.example.com/private/file.pdf?X-Amz-Signature=abc');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $s3Client->expects($this->once())
            ->method('getCommand')
            ->with('GetObject', ['Bucket' => 'secure-bucket', 'Key' => 'private/file.pdf'])
            ->willReturn($awsCommand);

        $s3Client->expects($this->once())
            ->method('createPresignedRequest')
            ->with($awsCommand, '+1 hour')
            ->willReturn($request);

        $handler = new GetPresignedUrlCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertSame('https://presigned-url.example.com/private/file.pdf?X-Amz-Signature=abc', $result);
    }

    public function testHandleReturnsPresignedUrlAsString(): void
    {
        $s3Client = $this->createMock(S3Client::class);
        $bus = $this->createMock(Bus::class);

        $command = new GetPresignedUrlCommand($bus);
        $command->inBucket('bucket')->byFilename('file.txt')->withExpiration(3600);

        $awsCommand = $this->createMock(AwsCommandInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $uri->method('__toString')->willReturn('https://signed-url.example.com/file.txt?sig=xyz');

        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $s3Client->method('getCommand')->willReturn($awsCommand);
        $s3Client->method('createPresignedRequest')->willReturn($request);

        $handler = new GetPresignedUrlCommandHandler($s3Client);
        $result = $handler->handle($command);

        $this->assertIsString($result);
        $this->assertSame('https://signed-url.example.com/file.txt?sig=xyz', $result);
    }
}

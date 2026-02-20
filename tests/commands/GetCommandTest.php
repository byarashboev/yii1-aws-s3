<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\GetCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class GetCommandTest extends TestCase
{
    private function makeCommand(): GetCommand
    {
        $bus = $this->createMock(Bus::class);
        return new GetCommand($bus);
    }

    public function testByFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->byFilename('path/to/file.jpg');

        $this->assertSame('path/to/file.jpg', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('my-bucket');

        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testSaveAs(): void
    {
        $command = $this->makeCommand();
        $result = $command->saveAs('/tmp/downloaded.jpg');

        $args = $command->toArgs();
        $this->assertSame('/tmp/downloaded.jpg', $args['SaveAs']);
        $this->assertSame($command, $result);
    }

    public function testIfMatch(): void
    {
        $command = $this->makeCommand();
        $result = $command->ifMatch('"etag-value"');

        $args = $command->toArgs();
        $this->assertSame('"etag-value"', $args['IfMatch']);
        $this->assertSame($command, $result);
    }

    public function testGetNameReturnsGetObject(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('GetObject', $command->getName());
    }

    public function testToArgsMergesOptionsOverArgs(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('bucket')->byFilename('file.txt');
        $command->withOptions(['RequestPayer' => 'requester', 'Bucket' => 'option-bucket']);

        $args = $command->toArgs();

        // args (set via inBucket/byFilename) must override options
        $this->assertSame('bucket', $args['Bucket']);
        $this->assertSame('file.txt', $args['Key']);
        $this->assertSame('requester', $args['RequestPayer']);
    }

    public function testAsyncDefaultsToFalse(): void
    {
        $command = $this->makeCommand();

        $this->assertFalse($command->isAsync());
    }

    public function testAsyncSetsFlag(): void
    {
        $command = $this->makeCommand();
        $result = $command->async();

        $this->assertTrue($command->isAsync());
        $this->assertSame($command, $result);
    }

    public function testGetFilenameDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getFilename());
    }

    public function testGetBucketDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getBucket());
    }
}

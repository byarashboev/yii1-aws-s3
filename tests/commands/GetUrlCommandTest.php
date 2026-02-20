<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\GetUrlCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class GetUrlCommandTest extends TestCase
{
    private function makeCommand(): GetUrlCommand
    {
        $bus = $this->createMock(Bus::class);
        return new GetUrlCommand($bus);
    }

    public function testByFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->byFilename('images/avatar.jpg');

        $this->assertSame('images/avatar.jpg', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('assets-bucket');

        $this->assertSame('assets-bucket', $command->getBucket());
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

    public function testByFilenameAndInBucketAreChainable(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('my-bucket')->byFilename('path/to/file.txt');

        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame('path/to/file.txt', $command->getFilename());
    }
}

<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\ListCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class ListCommandTest extends TestCase
{
    private function makeCommand(): ListCommand
    {
        $bus = $this->createMock(Bus::class);
        return new ListCommand($bus);
    }

    public function testByPrefix(): void
    {
        $command = $this->makeCommand();
        $result = $command->byPrefix('images/2024/');

        $this->assertSame('images/2024/', $command->getPrefix());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('my-bucket');

        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testGetNameReturnsListObjectsV2(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('ListObjectsV2', $command->getName());
    }

    public function testGetNameIsNotListObjects(): void
    {
        $command = $this->makeCommand();

        $this->assertNotSame('ListObjects', $command->getName());
    }

    public function testToArgsContainsAllSetValues(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('my-bucket')->byPrefix('photos/');

        $args = $command->toArgs();

        $this->assertSame('my-bucket', $args['Bucket']);
        $this->assertSame('photos/', $args['Prefix']);
    }

    public function testToArgsMergesOptionsOverArgs(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('real-bucket')->byPrefix('docs/');
        $command->withOptions(['MaxKeys' => 100, 'Bucket' => 'option-bucket']);

        $args = $command->toArgs();

        // args override options for overlapping keys
        $this->assertSame('real-bucket', $args['Bucket']);
        $this->assertSame('docs/', $args['Prefix']);
        $this->assertSame(100, $args['MaxKeys']);
    }

    public function testGetPrefixDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getPrefix());
    }

    public function testGetBucketDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getBucket());
    }
}

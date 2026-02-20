<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\ExistCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class ExistCommandTest extends TestCase
{
    private function makeCommand(): ExistCommand
    {
        $bus = $this->createMock(Bus::class);
        return new ExistCommand($bus);
    }

    public function testByFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->byFilename('documents/report.pdf');

        $this->assertSame('documents/report.pdf', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('my-bucket');

        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testWithOptions(): void
    {
        $command = $this->makeCommand();
        $options = ['@http' => ['connect_timeout' => 5]];
        $result = $command->withOptions($options);

        $this->assertSame($options, $command->getOptions());
        $this->assertSame($command, $result);
    }

    public function testGetOptionsDefaultsToEmptyArray(): void
    {
        $command = $this->makeCommand();

        $this->assertSame([], $command->getOptions());
    }

    public function testGetOptionsReturnsMergedOptions(): void
    {
        $command = $this->makeCommand();
        $command->withOptions(['key1' => 'value1']);
        $command->withOptions(['key2' => 'value2']);

        $options = $command->getOptions();

        $this->assertSame('value1', $options['key1']);
        $this->assertSame('value2', $options['key2']);
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

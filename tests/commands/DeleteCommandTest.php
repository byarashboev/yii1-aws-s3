<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\DeleteCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class DeleteCommandTest extends TestCase
{
    private function makeCommand(): DeleteCommand
    {
        $bus = $this->createMock(Bus::class);
        return new DeleteCommand($bus);
    }

    public function testByFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->byFilename('uploads/file.txt');

        $this->assertSame('uploads/file.txt', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('my-bucket');

        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testWithVersionId(): void
    {
        $command = $this->makeCommand();
        $result = $command->withVersionId('abc123version');

        $this->assertSame('abc123version', $command->getVersionId());
        $this->assertSame($command, $result);
    }

    public function testGetNameReturnsDeleteObject(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('DeleteObject', $command->getName());
    }

    public function testToArgsContainsAllSetValues(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('bucket')->byFilename('file.txt')->withVersionId('v1');

        $args = $command->toArgs();

        $this->assertSame('bucket', $args['Bucket']);
        $this->assertSame('file.txt', $args['Key']);
        $this->assertSame('v1', $args['VersionId']);
    }

    public function testToArgsMergesOptionsOverArgs(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('real-bucket')->byFilename('file.txt');
        $command->withOptions(['MFA' => 'mfa-value', 'Bucket' => 'option-bucket']);

        $args = $command->toArgs();

        // args override options for overlapping keys
        $this->assertSame('real-bucket', $args['Bucket']);
        $this->assertSame('mfa-value', $args['MFA']);
    }

    public function testGetVersionIdDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getVersionId());
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

<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\RestoreCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class RestoreCommandTest extends TestCase
{
    private function makeCommand(): RestoreCommand
    {
        $bus = $this->createMock(Bus::class);
        return new RestoreCommand($bus);
    }

    public function testWithLifetimeSetsNestedRestoreRequestDays(): void
    {
        $command = $this->makeCommand();
        $result = $command->withLifetime(7);

        $args = $command->toArgs();
        $this->assertSame(7, $args['RestoreRequest']['Days']);
        $this->assertSame($command, $result);
    }

    public function testWithLifetimeDoesNotSetTopLevelDays(): void
    {
        $command = $this->makeCommand();
        $command->withLifetime(7);

        $args = $command->toArgs();
        $this->assertArrayNotHasKey('Days', $args);
    }

    public function testGetLifetime(): void
    {
        $command = $this->makeCommand();
        $command->withLifetime(14);

        $this->assertSame(14, $command->getLifetime());
    }

    public function testGetLifetimeDefaultsToZero(): void
    {
        $command = $this->makeCommand();

        $this->assertSame(0, $command->getLifetime());
    }

    public function testByFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->byFilename('archive/file.tar.gz');

        $this->assertSame('archive/file.tar.gz', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('archive-bucket');

        $this->assertSame('archive-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testWithVersionId(): void
    {
        $command = $this->makeCommand();
        $result = $command->withVersionId('v123abc');

        $this->assertSame('v123abc', $command->getVersionId());
        $this->assertSame($command, $result);
    }

    public function testGetNameReturnsRestoreObject(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('RestoreObject', $command->getName());
    }

    public function testToArgsContainsAllSetValues(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('bucket')->byFilename('file.txt')->withLifetime(30)->withVersionId('v1');

        $args = $command->toArgs();

        $this->assertSame('bucket', $args['Bucket']);
        $this->assertSame('file.txt', $args['Key']);
        $this->assertSame(30, $args['RestoreRequest']['Days']);
        $this->assertSame('v1', $args['VersionId']);
    }

    public function testOptionsAreMergedIntoArgs(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('bucket')->byFilename('file.txt')->withLifetime(7);
        $command->withOption('RequestPayer', 'requester');

        $args = $command->toArgs();

        $this->assertSame('requester', $args['RequestPayer']);
        $this->assertSame('bucket', $args['Bucket']);
        $this->assertSame(7, $args['RestoreRequest']['Days']);
    }

    public function testArgsOverrideOptions(): void
    {
        $command = $this->makeCommand();
        $command->withOption('Bucket', 'options-bucket');
        $command->inBucket('args-bucket');

        $args = $command->toArgs();

        $this->assertSame('args-bucket', $args['Bucket']);
    }
}

<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\GetPresignedUrlCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class GetPresignedUrlCommandTest extends TestCase
{
    private function makeCommand(): GetPresignedUrlCommand
    {
        $bus = $this->createMock(Bus::class);
        return new GetPresignedUrlCommand($bus);
    }

    public function testByFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->byFilename('secure/document.pdf');

        $this->assertSame('secure/document.pdf', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('private-bucket');

        $this->assertSame('private-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testWithExpirationString(): void
    {
        $command = $this->makeCommand();
        $result = $command->withExpiration('+2 days');

        $this->assertSame('+2 days', $command->getExpiration());
        $this->assertSame($command, $result);
    }

    public function testWithExpirationInteger(): void
    {
        $command = $this->makeCommand();
        $command->withExpiration(3600);

        $this->assertSame(3600, $command->getExpiration());
    }

    public function testWithExpirationDateTime(): void
    {
        $command = $this->makeCommand();
        $date = new \DateTime('+1 hour');
        $command->withExpiration($date);

        $this->assertSame($date, $command->getExpiration());
    }

    public function testGetExpirationDefaultsToNull(): void
    {
        $command = $this->makeCommand();

        $this->assertNull($command->getExpiration());
    }

    public function testGetArgsContainsBucketAndKey(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('my-bucket')->byFilename('file.txt');

        $args = $command->getArgs();

        $this->assertSame('my-bucket', $args['Bucket']);
        $this->assertSame('file.txt', $args['Key']);
    }

    public function testGetArgsDefaultsToEmptyArray(): void
    {
        $command = $this->makeCommand();

        $this->assertSame([], $command->getArgs());
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

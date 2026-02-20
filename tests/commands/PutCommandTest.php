<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\PutCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class PutCommandTest extends TestCase
{
    private function makeCommand(): PutCommand
    {
        $bus = $this->createMock(Bus::class);
        return new PutCommand($bus);
    }

    public function testWithFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->withFilename('uploads/photo.png');

        $this->assertSame('uploads/photo.png', $command->getFilename());
        $this->assertSame($command, $result);
    }

    public function testInBucket(): void
    {
        $command = $this->makeCommand();
        $result = $command->inBucket('my-bucket');

        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame($command, $result);
    }

    public function testWithAcl(): void
    {
        $command = $this->makeCommand();
        $result = $command->withAcl('public-read');

        $this->assertSame('public-read', $command->getAcl());
        $this->assertSame($command, $result);
    }

    public function testWithBody(): void
    {
        $command = $this->makeCommand();
        $result = $command->withBody('file contents here');

        $this->assertSame('file contents here', $command->getBody());
        $this->assertSame($command, $result);
    }

    public function testWithContentType(): void
    {
        $command = $this->makeCommand();
        $result = $command->withContentType('image/png');

        $this->assertSame('image/png', $command->getContentType());
        $this->assertSame($command, $result);
    }

    public function testWithMetadata(): void
    {
        $command = $this->makeCommand();
        $meta = ['author' => 'test', 'version' => '1'];
        $result = $command->withMetadata($meta);

        $this->assertSame($meta, $command->getMetadata());
        $this->assertSame($command, $result);
    }

    public function testWithExpiration(): void
    {
        $command = $this->makeCommand();
        $date = new \DateTime('2025-12-31');
        $result = $command->withExpiration($date);

        $this->assertSame($date, $command->getExpiration());
        $this->assertSame($command, $result);
    }

    public function testGetNameReturnsPutObject(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('PutObject', $command->getName());
    }

    public function testToArgsMergesOptionsOverArgs(): void
    {
        $command = $this->makeCommand();
        $command->inBucket('real-bucket')->withFilename('file.txt')->withBody('data');
        $command->withOptions(['ServerSideEncryption' => 'AES256', 'Bucket' => 'option-bucket']);

        $args = $command->toArgs();

        // args override options for overlapping keys
        $this->assertSame('real-bucket', $args['Bucket']);
        $this->assertSame('file.txt', $args['Key']);
        $this->assertSame('data', $args['Body']);
        $this->assertSame('AES256', $args['ServerSideEncryption']);
    }

    public function testGetBodyDefaultsToNull(): void
    {
        $command = $this->makeCommand();

        $this->assertNull($command->getBody());
    }

    public function testGetMetadataDefaultsToEmptyArray(): void
    {
        $command = $this->makeCommand();

        $this->assertSame([], $command->getMetadata());
    }

    public function testGetContentTypeDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getContentType());
    }

    public function testGetExpirationDefaultsToNull(): void
    {
        $command = $this->makeCommand();

        $this->assertNull($command->getExpiration());
    }
}

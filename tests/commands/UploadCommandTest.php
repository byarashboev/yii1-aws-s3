<?php

namespace byarashboev\aws\s3\tests\commands;

use byarashboev\aws\s3\commands\UploadCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class UploadCommandTest extends TestCase
{
    private function makeCommand(): UploadCommand
    {
        $bus = $this->createMock(Bus::class);
        return new UploadCommand($bus);
    }

    public function testWithFilename(): void
    {
        $command = $this->makeCommand();
        $result = $command->withFilename('uploads/photo.jpg');

        $this->assertSame('uploads/photo.jpg', $command->getFilename());
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

    public function testWithSource(): void
    {
        $command = $this->makeCommand();
        $result = $command->withSource('/tmp/local-file.jpg');

        $this->assertSame('/tmp/local-file.jpg', $command->getSource());
        $this->assertSame($command, $result);
    }

    public function testWithPartSize(): void
    {
        $command = $this->makeCommand();
        $result = $command->withPartSize(5242880);

        $this->assertSame(5242880, $command->getPartSize());
        $this->assertSame($command, $result);
    }

    public function testWithConcurrency(): void
    {
        $command = $this->makeCommand();
        $result = $command->withConcurrency(5);

        $this->assertSame(5, $command->getConcurrency());
        $this->assertSame($command, $result);
    }

    public function testWithMupThreshold(): void
    {
        $command = $this->makeCommand();
        $result = $command->withMupThreshold(10485760);

        $this->assertSame(10485760, $command->getMupThreshold());
        $this->assertSame($command, $result);
    }

    public function testWithContentType(): void
    {
        $command = $this->makeCommand();
        $result = $command->withContentType('image/jpeg');

        $this->assertSame('image/jpeg', $command->getContentType());
        $this->assertSame($command, $result);
    }

    public function testWithContentDisposition(): void
    {
        $command = $this->makeCommand();
        $result = $command->withContentDisposition('attachment; filename="file.jpg"');

        $this->assertSame('attachment; filename="file.jpg"', $command->getContentDisposition());
        $this->assertSame($command, $result);
    }

    public function testWithParam(): void
    {
        $command = $this->makeCommand();
        $result = $command->withParam('ServerSideEncryption', 'AES256');

        $this->assertSame('AES256', $command->getParam('ServerSideEncryption'));
        $this->assertSame($command, $result);
    }

    public function testGetOptionsContainsPartSize(): void
    {
        $command = $this->makeCommand();
        $command->withPartSize(5242880)->withConcurrency(3);

        $options = $command->getOptions();

        $this->assertSame(5242880, $options['part_size']);
        $this->assertSame(3, $options['concurrency']);
    }

    public function testGetOptionsContainsParams(): void
    {
        $command = $this->makeCommand();
        $command->withContentType('video/mp4')->withParam('ACL', 'private');

        $options = $command->getOptions();

        $this->assertSame('video/mp4', $options['params']['ContentType']);
        $this->assertSame('private', $options['params']['ACL']);
    }

    public function testGetPartSizeDefaultsToZero(): void
    {
        $command = $this->makeCommand();

        $this->assertSame(0, $command->getPartSize());
    }

    public function testGetConcurrencyDefaultsToZero(): void
    {
        $command = $this->makeCommand();

        $this->assertSame(0, $command->getConcurrency());
    }

    public function testGetMupThresholdDefaultsToZero(): void
    {
        $command = $this->makeCommand();

        $this->assertSame(0, $command->getMupThreshold());
    }

    public function testGetContentTypeDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getContentType());
    }

    public function testGetContentDispositionDefaultsToEmptyString(): void
    {
        $command = $this->makeCommand();

        $this->assertSame('', $command->getContentDisposition());
    }

    public function testGetParamReturnsDefaultWhenNotSet(): void
    {
        $command = $this->makeCommand();

        $this->assertNull($command->getParam('NonExistent'));
        $this->assertSame('default', $command->getParam('NonExistent', 'default'));
    }

    public function testAsyncDefaultsToFalse(): void
    {
        $command = $this->makeCommand();

        $this->assertFalse($command->isAsync());
    }

    public function testAsyncSetsFlag(): void
    {
        $command = $this->makeCommand();
        $command->async();

        $this->assertTrue($command->isAsync());
    }
}

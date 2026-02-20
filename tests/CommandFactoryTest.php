<?php

namespace byarashboev\aws\s3\tests;

use byarashboev\aws\s3\CommandBuilder;
use byarashboev\aws\s3\CommandFactory;
use byarashboev\aws\s3\commands\DeleteCommand;
use byarashboev\aws\s3\commands\ExistCommand;
use byarashboev\aws\s3\commands\GetCommand;
use byarashboev\aws\s3\commands\GetPresignedUrlCommand;
use byarashboev\aws\s3\commands\GetUrlCommand;
use byarashboev\aws\s3\commands\ListCommand;
use byarashboev\aws\s3\commands\PutCommand;
use byarashboev\aws\s3\commands\RestoreCommand;
use byarashboev\aws\s3\commands\UploadCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class CommandFactoryTest extends TestCase
{
    private CommandFactory $factory;
    private Bus $bus;

    protected function setUp(): void
    {
        $this->bus = $this->createMock(Bus::class);
        $builder = new CommandBuilder($this->bus, 'default-bucket', 'public-read');
        $this->factory = new CommandFactory($builder);
    }

    public function testGetReturnsGetCommandWithFilename(): void
    {
        $command = $this->factory->get('file.txt');

        $this->assertInstanceOf(GetCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
    }

    public function testGetSetsDefaultBucket(): void
    {
        $command = $this->factory->get('file.txt');

        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testPutReturnsPutCommandWithFilenameAndBody(): void
    {
        $command = $this->factory->put('file.txt', 'body content');

        $this->assertInstanceOf(PutCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
        $this->assertSame('body content', $command->getBody());
    }

    public function testPutSetsDefaultBucketAndAcl(): void
    {
        $command = $this->factory->put('file.txt', 'body');

        $this->assertSame('default-bucket', $command->getBucket());
        $this->assertSame('public-read', $command->getAcl());
    }

    public function testDeleteReturnsDeleteCommandWithFilename(): void
    {
        $command = $this->factory->delete('file.txt');

        $this->assertInstanceOf(DeleteCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
    }

    public function testDeleteSetsDefaultBucket(): void
    {
        $command = $this->factory->delete('file.txt');

        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testUploadReturnsUploadCommandWithFilenameAndSource(): void
    {
        $command = $this->factory->upload('file.txt', '/tmp/file');

        $this->assertInstanceOf(UploadCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
        $this->assertSame('/tmp/file', $command->getSource());
    }

    public function testUploadSetsDefaultBucketAndAcl(): void
    {
        $command = $this->factory->upload('file.txt', '/tmp/file');

        $this->assertSame('default-bucket', $command->getBucket());
        $this->assertSame('public-read', $command->getAcl());
    }

    public function testRestoreReturnsRestoreCommandWithFilenameAndLifetime(): void
    {
        $command = $this->factory->restore('file.txt', 7);

        $this->assertInstanceOf(RestoreCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
        $this->assertSame(7, $command->getLifetime());
    }

    public function testRestoreSetsDefaultBucket(): void
    {
        $command = $this->factory->restore('file.txt', 7);

        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testListReturnsListCommandWithPrefix(): void
    {
        $command = $this->factory->list('prefix/');

        $this->assertInstanceOf(ListCommand::class, $command);
        $this->assertSame('prefix/', $command->getPrefix());
    }

    public function testListSetsDefaultBucket(): void
    {
        $command = $this->factory->list('prefix/');

        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testExistReturnsExistCommandWithFilename(): void
    {
        $command = $this->factory->exist('file.txt');

        $this->assertInstanceOf(ExistCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
    }

    public function testExistSetsDefaultBucket(): void
    {
        $command = $this->factory->exist('file.txt');

        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testGetUrlReturnsGetUrlCommandWithFilename(): void
    {
        $command = $this->factory->getUrl('file.txt');

        $this->assertInstanceOf(GetUrlCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
    }

    public function testGetUrlSetsDefaultBucket(): void
    {
        $command = $this->factory->getUrl('file.txt');

        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testGetPresignedUrlReturnsGetPresignedUrlCommandWithFilenameAndExpiration(): void
    {
        $command = $this->factory->getPresignedUrl('file.txt', '+2 days');

        $this->assertInstanceOf(GetPresignedUrlCommand::class, $command);
        $this->assertSame('file.txt', $command->getFilename());
        $this->assertSame('+2 days', $command->getExpiration());
    }

    public function testGetPresignedUrlSetsDefaultBucket(): void
    {
        $command = $this->factory->getPresignedUrl('file.txt', '+1 hour');

        $this->assertSame('default-bucket', $command->getBucket());
    }
}

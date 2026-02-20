<?php

namespace byarashboev\aws\s3\tests;

use byarashboev\aws\s3\CommandBuilder;
use byarashboev\aws\s3\commands\ExistCommand;
use byarashboev\aws\s3\commands\GetCommand;
use byarashboev\aws\s3\commands\GetUrlCommand;
use byarashboev\aws\s3\commands\PutCommand;
use byarashboev\aws\s3\interfaces\Bus;
use PHPUnit\Framework\TestCase;

class CommandBuilderTest extends TestCase
{
    private Bus $bus;

    protected function setUp(): void
    {
        $this->bus = $this->createMock(Bus::class);
    }

    public function testBuildGetCommandReturnGetCommandWithDefaultBucket(): void
    {
        $builder = new CommandBuilder($this->bus, 'default-bucket', 'public-read');

        $command = $builder->build(GetCommand::class);

        $this->assertInstanceOf(GetCommand::class, $command);
        $this->assertSame('default-bucket', $command->getBucket());
    }

    public function testBuildPutCommandSetsDefaultBucketAndAcl(): void
    {
        $builder = new CommandBuilder($this->bus, 'my-bucket', 'private');

        $command = $builder->build(PutCommand::class);

        $this->assertInstanceOf(PutCommand::class, $command);
        $this->assertSame('my-bucket', $command->getBucket());
        $this->assertSame('private', $command->getAcl());
    }

    public function testBuildGetUrlCommandSetsDefaultBucketButNotAcl(): void
    {
        $builder = new CommandBuilder($this->bus, 'assets-bucket', 'public-read');

        $command = $builder->build(GetUrlCommand::class);

        $this->assertInstanceOf(GetUrlCommand::class, $command);
        $this->assertSame('assets-bucket', $command->getBucket());
        // GetUrlCommand does not implement HasAcl, so we only check bucket
    }

    public function testBuildExistCommandSetsDefaultBucket(): void
    {
        $builder = new CommandBuilder($this->bus, 'check-bucket', '');

        $command = $builder->build(ExistCommand::class);

        $this->assertInstanceOf(ExistCommand::class, $command);
        $this->assertSame('check-bucket', $command->getBucket());
    }

    public function testBuildWithEmptyBucketDoesNotError(): void
    {
        $builder = new CommandBuilder($this->bus, '', '');

        $command = $builder->build(GetCommand::class);

        $this->assertInstanceOf(GetCommand::class, $command);
        $this->assertSame('', $command->getBucket());
    }

    public function testBuildWithEmptyAclDoesNotError(): void
    {
        $builder = new CommandBuilder($this->bus, 'bucket', '');

        $command = $builder->build(PutCommand::class);

        $this->assertInstanceOf(PutCommand::class, $command);
        $this->assertSame('', $command->getAcl());
    }

    public function testBuildGetCommandDoesNotSetAcl(): void
    {
        $builder = new CommandBuilder($this->bus, 'bucket', 'public-read');

        /** @var GetCommand $command */
        $command = $builder->build(GetCommand::class);

        // GetCommand does not have getAcl(), but verifying it has no ACL in args via toArgs
        $args = $command->toArgs();
        $this->assertArrayNotHasKey('ACL', $args);
    }
}

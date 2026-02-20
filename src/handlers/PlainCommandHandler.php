<?php

namespace byarashboev\aws\s3\handlers;

use Aws\CommandInterface as AwsCommand;
use byarashboev\aws\s3\base\handlers\Handler;
use byarashboev\aws\s3\interfaces\commands\Asynchronous;
use byarashboev\aws\s3\interfaces\commands\Command;
use byarashboev\aws\s3\interfaces\commands\PlainCommand;

/**
 * Class PlainCommandHandler
 *
 * @package byarashboev\aws\s3\handlers
 */
final class PlainCommandHandler extends Handler
{
    /**
     * @param Command $command
     *
     * @return \Aws\ResultInterface|\GuzzleHttp\Promise\PromiseInterface
     */
    public function handle(Command $command)
    {
        /** @var PlainCommand $command */
        $awsCommand = $this->transformToAwsCommand($command);

        /** @var \GuzzleHttp\Promise\PromiseInterface $promise */
        $promise = $this->s3Client->executeAsync($awsCommand);

        return $this->commandIsAsync($command) ? $promise : $promise->wait();
    }

    /**
     * @param Command $command
     *
     * @return bool
     */
    protected function commandIsAsync(Command $command): bool
    {
        return $command instanceof Asynchronous && $command->isAsync();
    }

    /**
     * @param PlainCommand $command
     *
     * @return AwsCommand
     */
    protected function transformToAwsCommand(PlainCommand $command): AwsCommand
    {
        $args = array_filter($command->toArgs(), function ($value) {
            return $value !== null;
        });

        return $this->s3Client->getCommand($command->getName(), $args);
    }
}

<?php

namespace byarashboev\aws\s3\handlers;

use byarashboev\aws\s3\base\handlers\Handler;
use byarashboev\aws\s3\commands\GetPresignedUrlCommand;
use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Class GetPresignedUrlCommandHandler
 *
 * @package byarashboev\aws\s3\handlers
 */
final class GetPresignedUrlCommandHandler extends Handler
{
    /**
     * @param Command $command
     *
     * @return string
     */
    public function handle(Command $command): string
    {
        /** @var GetPresignedUrlCommand $command */
        $awsCommand = $this->s3Client->getCommand('GetObject', $command->getArgs());
        $request = $this->s3Client->createPresignedRequest($awsCommand, $command->getExpiration());

        return (string) $request->getUri();
    }
}

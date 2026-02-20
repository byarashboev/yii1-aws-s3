<?php

namespace byarashboev\aws\s3\handlers;

use byarashboev\aws\s3\base\handlers\Handler;
use byarashboev\aws\s3\commands\GetUrlCommand;
use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Class GetUrlCommandHandler
 *
 * @package byarashboev\aws\s3\handlers
 */
final class GetUrlCommandHandler extends Handler
{
    /**
     * @param Command $command
     *
     * @return string
     */
    public function handle(Command $command): string
    {
        /** @var GetUrlCommand $command */
        return $this->s3Client->getObjectUrl($command->getBucket(), $command->getFilename());
    }
}

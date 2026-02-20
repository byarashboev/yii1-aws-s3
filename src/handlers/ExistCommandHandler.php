<?php

namespace byarashboev\aws\s3\handlers;

use byarashboev\aws\s3\base\handlers\Handler;
use byarashboev\aws\s3\commands\ExistCommand;
use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Class ExistCommandHandler
 *
 * @package byarashboev\aws\s3\handlers
 */
final class ExistCommandHandler extends Handler
{
    /**
     * @param Command $command
     *
     * @return bool
     */
    public function handle(Command $command): bool
    {
        /** @var ExistCommand $command */
        return $this->s3Client->doesObjectExist(
            $command->getBucket(),
            $command->getFilename(),
            $command->getOptions()
        );
    }
}

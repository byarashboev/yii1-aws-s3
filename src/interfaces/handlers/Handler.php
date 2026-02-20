<?php

namespace byarashboev\aws\s3\interfaces\handlers;

use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Interface Handler
 *
 * @package byarashboev\aws\s3\interfaces\handlers
 */
interface Handler
{
    /**
     * @param Command $command
     *
     * @return mixed
     */
    public function handle(Command $command);
}

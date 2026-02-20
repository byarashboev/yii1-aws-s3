<?php

namespace byarashboev\aws\s3\interfaces;

use byarashboev\aws\s3\interfaces\commands\Command;
use byarashboev\aws\s3\interfaces\handlers\Handler;

/**
 * Interface HandlerResolver
 *
 * @package byarashboev\aws\s3\interfaces
 */
interface HandlerResolver
{
    /**
     * @param Command $command
     *
     * @return Handler
     */
    public function resolve(Command $command): Handler;
}

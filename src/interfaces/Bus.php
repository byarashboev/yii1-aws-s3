<?php

namespace byarashboev\aws\s3\interfaces;

use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Interface Bus
 *
 * @package byarashboev\aws\s3\interfaces
 */
interface Bus
{
    /**
     * @param Command $command
     *
     * @return mixed
     */
    public function execute(Command $command);
}

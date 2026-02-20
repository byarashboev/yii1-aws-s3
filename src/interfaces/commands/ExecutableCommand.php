<?php

namespace byarashboev\aws\s3\interfaces\commands;

/**
 * Interface ExecutableCommand
 *
 * @package byarashboev\aws\s3\interfaces\commands
 */
interface ExecutableCommand extends Command
{
    /**
     * @return mixed
     */
    public function execute();
}

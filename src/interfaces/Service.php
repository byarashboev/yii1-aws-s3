<?php

namespace byarashboev\aws\s3\interfaces;

use byarashboev\aws\s3\CommandFactory;
use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Interface Service
 *
 * @package byarashboev\aws\s3\interfaces
 */
interface Service
{
    /**
     * @param Command $command
     *
     * @return mixed
     */
    public function execute(Command $command);

    /**
     * @param string $commandClass
     *
     * @return Command
     */
    public function create(string $commandClass): Command;

    /**
     * @return CommandFactory
     */
    public function commands(): CommandFactory;
}

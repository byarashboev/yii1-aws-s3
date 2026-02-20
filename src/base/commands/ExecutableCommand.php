<?php

namespace byarashboev\aws\s3\base\commands;

use byarashboev\aws\s3\interfaces\Bus;
use byarashboev\aws\s3\interfaces\commands\ExecutableCommand as ExecutableCommandInterface;

/**
 * Class ExecutableCommand
 *
 * @package byarashboev\aws\s3\base\commands
 */
abstract class ExecutableCommand implements ExecutableCommandInterface
{
    /** @var Bus */
    private $bus;

    /**
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        return $this->bus->execute($this);
    }
}

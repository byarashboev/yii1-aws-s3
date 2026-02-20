<?php

namespace byarashboev\aws\s3\interfaces\commands;

/**
 * Interface Asynchronous
 *
 * @package byarashboev\aws\s3\interfaces\commands
 */
interface Asynchronous
{
    /**
     * @return $this
     */
    public function async();

    /**
     * @return bool
     */
    public function isAsync(): bool;
}

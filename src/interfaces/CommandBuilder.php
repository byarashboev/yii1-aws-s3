<?php

namespace byarashboev\aws\s3\interfaces;

use byarashboev\aws\s3\interfaces\commands\Command;

/**
 * Interface CommandBuilder
 *
 * @package byarashboev\aws\s3\interfaces
 */
interface CommandBuilder
{
    /**
     * @param string $className
     *
     * @return Command
     */
    public function build(string $className): Command;
}

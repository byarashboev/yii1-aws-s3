<?php

namespace byarashboev\aws\s3\interfaces\commands;

/**
 * Interface HasAcl
 *
 * @package byarashboev\aws\s3\interfaces\commands
 */
interface HasAcl
{
    /**
     * @return string
     */
    public function getAcl(): string;

    /**
     * @param string $acl
     *
     * @return $this
     */
    public function withAcl(string $acl);
}

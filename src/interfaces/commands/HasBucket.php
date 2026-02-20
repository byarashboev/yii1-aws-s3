<?php

namespace byarashboev\aws\s3\interfaces\commands;

/**
 * Interface HasBucket
 *
 * @package byarashboev\aws\s3\interfaces\commands
 */
interface HasBucket
{
    /**
     * @return string
     */
    public function getBucket(): string;

    /**
     * @param string $bucket
     *
     * @return $this
     */
    public function inBucket(string $bucket);
}

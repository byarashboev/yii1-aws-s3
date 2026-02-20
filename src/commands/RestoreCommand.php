<?php

namespace byarashboev\aws\s3\commands;

use Aws\ResultInterface;
use byarashboev\aws\s3\base\commands\ExecutableCommand;
use byarashboev\aws\s3\base\commands\traits\Async;
use byarashboev\aws\s3\base\commands\traits\Options;
use byarashboev\aws\s3\interfaces\commands\Asynchronous;
use byarashboev\aws\s3\interfaces\commands\HasBucket;
use byarashboev\aws\s3\interfaces\commands\PlainCommand;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Class RestoreCommand
 *
 * @method ResultInterface|PromiseInterface execute()
 *
 * @package byarashboev\aws\s3\commands
 */
class RestoreCommand extends ExecutableCommand implements PlainCommand, HasBucket, Asynchronous
{
    use Async;
    use Options;

    /** @var array */
    protected $args = [];

    /**
     * @return string
     */
    public function getBucket(): string
    {
        return $this->args['Bucket'] ?? '';
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function inBucket(string $name)
    {
        $this->args['Bucket'] = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->args['Key'] ?? '';
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function byFilename(string $filename)
    {
        $this->args['Key'] = $filename;

        return $this;
    }

    /**
     * @return int lifetime of the active copy in days
     */
    public function getLifetime(): int
    {
        return $this->args['RestoreRequest']['Days'] ?? 0;
    }

    /**
     * @param int $days lifetime of the active copy in days
     *
     * @return $this
     */
    public function withLifetime(int $days)
    {
        $this->args['RestoreRequest']['Days'] = $days;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersionId(): string
    {
        return $this->args['VersionId'] ?? '';
    }

    /**
     * @param string $versionId
     *
     * @return $this
     */
    public function withVersionId(string $versionId)
    {
        $this->args['VersionId'] = $versionId;

        return $this;
    }

    /**
     * @internal used by the handlers
     *
     * @return string
     */
    public function getName(): string
    {
        return 'RestoreObject';
    }

    /**
     * @internal used by the handlers
     *
     * @return array
     */
    public function toArgs(): array
    {
        return array_replace($this->options, $this->args);
    }
}

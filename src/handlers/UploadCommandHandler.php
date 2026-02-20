<?php

namespace byarashboev\aws\s3\handlers;

use byarashboev\aws\s3\commands\UploadCommand;
use byarashboev\aws\s3\base\handlers\Handler;
use byarashboev\aws\s3\interfaces\commands\Command;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

/**
 * Class UploadCommandHandler
 *
 * @package byarashboev\aws\s3\handlers
 */
final class UploadCommandHandler extends Handler
{
    /**
     * @param Command $command
     *
     * @return \Aws\ResultInterface|\GuzzleHttp\Promise\PromiseInterface
     */
    public function handle(Command $command)
    {
        /** @var UploadCommand $command */
        $source = $this->sourceToStream($command->getSource());
        $options = array_filter($command->getOptions(), function ($value) {
            return $value !== null;
        });

        $promise = $this->s3Client->uploadAsync(
            $command->getBucket(),
            $command->getFilename(),
            $source,
            $command->getAcl(),
            $options
        );

        return $command->isAsync() ? $promise : $promise->wait();
    }

    /**
     * Create a new stream based on the input type.
     *
     * @param resource|string|StreamInterface $source path to a local file, resource or stream
     *
     * @return StreamInterface
     */
    protected function sourceToStream($source): StreamInterface
    {
        if (is_string($source)) {
            $source = Utils::tryFopen($source, 'r');
        }

        return Utils::streamFor($source);
    }
}

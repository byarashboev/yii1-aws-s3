<?php

namespace byarashboev\aws\s3;

use Aws\S3\S3Client;
use byarashboev\aws\s3\handlers\PlainCommandHandler;
use byarashboev\aws\s3\interfaces;

/**
 * Class HandlerResolver
 *
 * @package byarashboev\aws\s3
 */
class HandlerResolver implements interfaces\HandlerResolver
{
    /** @var array */
    protected $handlers = [];

    /** @var string */
    protected $plainCommandHandlerClassName = PlainCommandHandler::class;

    /** @var S3Client */
    protected $s3Client;

    /**
     * @param S3Client $s3Client
     * @param array    $config
     */
    public function __construct(S3Client $s3Client, array $config = [])
    {
        foreach ($config as $name => $value) {
            $this->{$name} = $value;
        }
        $this->s3Client = $s3Client;
    }

    /**
     * @param interfaces\commands\Command $command
     *
     * @return interfaces\handlers\Handler
     * @throws \CException
     */
    public function resolve(interfaces\commands\Command $command): interfaces\handlers\Handler
    {
        $commandClass = get_class($command);

        if (isset($this->handlers[$commandClass])) {
            $handler = $this->handlers[$commandClass];

            return is_object($handler) ? $handler : $this->createHandler($handler);
        }

        if ($command instanceof interfaces\commands\PlainCommand) {
            return $this->createHandler($this->plainCommandHandlerClassName);
        }

        $handlerClass = $commandClass . 'Handler';
        if (class_exists($handlerClass)) {
            return $this->createHandler($handlerClass);
        }

        $handlerClass = str_replace('\\commands\\', '\\handlers\\', $handlerClass);
        if (class_exists($handlerClass)) {
            return $this->createHandler($handlerClass);
        }

        throw new \CException("Could not resolve the handler for command \"{$commandClass}\"");
    }

    /**
     * @param string $commandClass
     * @param mixed  $handler
     */
    public function bindHandler(string $commandClass, $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    /**
     * @param array $handlers
     */
    public function setHandlers(array $handlers): void
    {
        foreach ($handlers as $commandClass => $handler) {
            $this->bindHandler($commandClass, $handler);
        }
    }

    /**
     * @param string $className
     */
    public function setPlainCommandHandler(string $className): void
    {
        $this->plainCommandHandlerClassName = $className;
    }

    /**
     * @param string $type
     *
     * @return interfaces\handlers\Handler
     */
    protected function createHandler(string $type): interfaces\handlers\Handler
    {
        return new $type($this->s3Client);
    }
}

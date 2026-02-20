<?php

namespace byarashboev\aws\s3;

use byarashboev\aws\s3\interfaces;

/**
 * Class Bus
 *
 * @package byarashboev\aws\s3
 */
class Bus implements interfaces\Bus
{
    /** @var interfaces\HandlerResolver */
    protected $resolver;

    /**
     * @param interfaces\HandlerResolver $resolver
     */
    public function __construct(interfaces\HandlerResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param interfaces\commands\Command $command
     *
     * @return mixed
     */
    public function execute(interfaces\commands\Command $command)
    {
        $handler = $this->resolver->resolve($command);

        return $handler->handle($command);
    }
}

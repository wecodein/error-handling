<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Handler;

final class HandlerAggregate implements HandlerInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    public function __construct(HandlerInterface ...$handlers)
    {
        $this->handlers = $handlers;
    }

    public function register()
    {
        foreach ($this->handlers as $handler) {
            $handler->register();
        }
    }

    public function restore()
    {
        foreach ($this->handlers as $handler) {
            $handler->restore();
        }
    }
}

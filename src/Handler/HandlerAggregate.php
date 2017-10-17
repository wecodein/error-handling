<?php
/**
 * This file is part of the error-handling package.
 *
 * Copyright (c) Dusan Vejin
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Handler;

final class HandlerAggregate implements HandlerInterface
{
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

<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Handler;

use PHPUnit\Framework\TestCase;
use WeCodeIn\ErrorHandling\Handler\HandlerAggregate;
use WeCodeIn\ErrorHandling\Handler\HandlerInterface;

final class HandlerAggregateTest extends TestCase
{
    public function testRegisteringMultipleHandlers()
    {
        $handlers = [];

        for ($i = 0; $i < 3; $i++) {
            $handler = $this->createMock(HandlerInterface::class);
            $handler->expects($this->once())->method('register');

            $handlers[] = $handler;
        }

        $handlerAggregate = new HandlerAggregate(...$handlers);
        $handlerAggregate->register();
    }

    public function testRestoringMultipleHandlers()
    {
        $handlers = [];

        for ($i = 0; $i < 3; $i++) {
            $handler = $this->createMock(HandlerInterface::class);
            $handler->expects($this->once())->method('restore');

            $handlers[] = $handler;
        }

        $handlerAggregate = new HandlerAggregate(...$handlers);
        $handlerAggregate->restore();
    }
}

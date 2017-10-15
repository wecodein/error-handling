<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use WeCodeIn\ErrorHandling\Handler\AbstractHandler;

final class AbstractHandlerTest extends TestCase
{
    use PHPMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $handler;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(AbstractHandler::class);
    }

    public function testRegisterInvokesRegisterListener()
    {
        $this->handler->expects($this->once())
            ->method('registerListener');

        $this->handler->register();
    }

    public function testRegisterWithConsecutiveCallsInvokesRegisterListenerOnce()
    {
        $this->handler->expects($this->once())
            ->method('registerListener');

        for ($i = 0; $i < 2; $i++) {
            $this->handler->register();
        }
    }

    public function testRestoreWhenRegisteredInvokesRestoreListener()
    {
        $this->handler->register();

        $this->handler->expects($this->once())
            ->method('restoreListener');

        $this->handler->restore();
    }

    public function testRestoreWhenNotRegisteredNotInvokesRestoreListener()
    {
        $this->handler->expects($this->never())
            ->method('restoreListener');

        $this->handler->restore();
    }

    public function testRestoreWithConsecutiveCallsWhenRegisteredInvokesRestoreListenerOnce()
    {
        $this->handler->register();

        $this->handler->expects($this->once())
            ->method('restoreListener');

        for ($i = 0; $i < 2; $i++) {
            $this->handler->restore();
        }
    }
}

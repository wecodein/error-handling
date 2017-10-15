<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_Matcher_Invocation as Invocation;
use WeCodeIn\ErrorHandling\Handler\AbstractHandler;

final class AbstractHandlerTest extends TestCase
{
    use PHPMock;

    public function testRegisterInvokesRegisterListener()
    {
        $handler = $this->getMockForHandler($this->once());
        $handler->register();
    }

    public function testRegisterWithConsecutiveCallsInvokesRegisterListenerOnce()
    {
        $handler = $this->getMockForHandler($this->once());

        for ($i = 0; $i < 2; $i++) {
            $handler->register();
        }
    }

    public function testRestoreWhenRegisteredInvokesRestoreListener()
    {
        $handler = $this->getMockForHandler($this->once());
        $handler->register();
        $handler->restore();
    }

    public function testRestoreWhenNotRegisteredNotInvokesRestoreListener()
    {
        $handler = $this->getMockForHandler($this->never());
        $handler->restore();
    }

    public function testRestoreWithConsecutiveCallsWhenRegisteredInvokesRestoreListenerOnce()
    {
        $handler = $this->getMockForHandler($this->once());
        $handler->register();

        for ($i = 0; $i < 2; $i++) {
            $handler->restore();
        }
    }

    protected function getMockForHandler(Invocation $matcher)
    {
        $handler = $this->getMockForAbstractClass(AbstractHandler::class);
        $handler->expects($matcher)
            ->method('registerListener');
        return $handler;
    }
}

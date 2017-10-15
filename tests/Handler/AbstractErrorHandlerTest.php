<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use WeCodeIn\ErrorHandling\Handler\AbstractErrorHandler;

final class AbstractErrorHandlerTest extends TestCase
{
    use PHPMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $handler;
    /** @var Closure */
    protected $listener;

    public function setUp()
    {
        $this->setErrorReporting(E_ALL);
        $this->handler = $this->getMockForAbstractClass(AbstractErrorHandler::class);

        $reflectionClass = $this->getErrorHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_error_handler')
            ->expects($this->any())
            ->willReturnCallback(
                function ($callable) {
                    $this->listener = $callable;
                }
            );
    }

    public function testRegistersListenerToStandardPHPErrorHandler()
    {
        $this->handler->register();

        $this->assertInternalType('callable', $this->listener);
    }

    public function testRestoresListenerFromStandardPHPErrorHandler()
    {
        $this->handler->register();

        $reflectionClass = $this->getErrorHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'restore_error_handler')
            ->expects($this->once());

        $this->handler->restore();
    }

    public function testRespectsPHPErrorReportingOnError()
    {
        $this->setErrorReporting(0);
        $this->handler->register();

        $this->handler->expects($this->never())
            ->method('handle');

        $this->triggerPHPError();
    }

    public function testCallsHandleMethodOnError()
    {
        $this->handler->register();

        $this->handler->expects($this->once())
            ->method('handle');

        $this->triggerPHPError();
    }

    protected function getErrorHandlerReflectionClass()
    {
        return new ReflectionClass(AbstractErrorHandler::class);
    }

    protected function setErrorReporting(int $level) : int
    {
        return error_reporting($level);
    }

    protected function triggerPHPError()
    {
        ($this->listener)(E_WARNING, 'Error message', 'file.php', 1);
    }
}

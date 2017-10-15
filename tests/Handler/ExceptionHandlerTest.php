<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use Exception;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

final class ExceptionHandlerTest extends TestCase
{
    use PHPMock;

    /** @var Closure */
    protected $listener;

    public function setUp()
    {
        $this->setErrorReporting(E_ALL);

        $reflectionClass = $this->getExceptionHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_exception_handler')
            ->expects($this->any())
            ->willReturnCallback(
                function ($callable) {
                    $this->listener = $callable;
                }
            );
    }

    public function testRegistersListenerToStandardPHPExceptionHandler()
    {
        $handler = new ExceptionHandler();
        $handler->register();

        $this->assertInternalType('callable', $this->listener);
    }

    public function testRestoresListenerFromStandardPHPExceptionHandler()
    {
        $handler = new ExceptionHandler();
        $handler->register();

        $reflectionClass = $this->getExceptionHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'restore_exception_handler')
            ->expects($this->once());

        $handler->restore();
    }

    public function testInvokeInvokesProcessorsStack()
    {
        $processor1 = $this->getMockForProcessor();
        $processor1->expects($this->once())
            ->method('__invoke')
            ->willReturn($this->createMock(Exception::class));

        $processor2 = $this->getMockForProcessor();
        $processor2->expects($this->once())
            ->method('__invoke')
            ->willReturn($this->createMock(Exception::class));

        $processors = [$processor1, $processor2];

        $handler = new ExceptionHandler(...$processors);
        $handler->register();
        $handler(new Exception());
    }

    public function testInvokePassThrowableTroughStack()
    {
        $processor1 = $this->getMockForProcessor();
        $processor1->expects($this->any())
            ->method('__invoke')
            ->willReturn($processor1WillReturn = $this->createMock(Exception::class));

        $processor2 = $this->getMockForProcessor();
        $processor2->expects($this->any())
            ->method('__invoke')
            ->willReturnCallback(function (Throwable $throwable) use (&$processor2WillReceive) {
                $processor2WillReceive = $throwable;
                return $throwable;
            });

        $processors = [$processor1, $processor2];

        $handler = new ExceptionHandler(...$processors);
        $handler->register();
        $handler(new Exception());

        $this->assertSame($processor1WillReturn, $processor2WillReceive);
    }

    protected function setErrorReporting(int $level) : int
    {
        return error_reporting($level);
    }

    protected function getExceptionHandlerReflectionClass()
    {
        return new ReflectionClass(ExceptionHandler::class);
    }

    protected function getMockForSetExceptionHandler()
    {
        $reflectionClass = $this->getExceptionHandlerReflectionClass();
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_exception_handler');
    }

    protected function getMockForRestoreExceptionHandler()
    {
        $reflectionClass = $this->getExceptionHandlerReflectionClass();
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'restore_exception_handler');
    }

    protected function getMockForProcessor()
    {
        return $this->createMock(ProcessorInterface::class);
    }
}

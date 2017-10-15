<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use Exception;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;
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

    public function testPassingThrowableThroughPipeline()
    {
        $processors = [
            new CallableProcessor(function () {
                return new RuntimeException('New exception');
            }),
            new CallableProcessor(function (Throwable $throwable) {
                $this->assertInstanceOf(RuntimeException::class, $throwable);
                $this->assertSame('New exception', $throwable->getMessage());

                return $throwable;
            }),
        ];

        $handler = new ExceptionHandler($processors);
        $handler->register();
        $handler(new Exception());
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

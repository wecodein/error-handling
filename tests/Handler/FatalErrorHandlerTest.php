<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use Exception;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

final class FatalErrorHandlerTest extends TestCase
{
    use PHPMock;

    /** @var Closure */
    protected $listener;

    public function setUp()
    {
        $this->setErrorReporting(E_ALL);

        $reflectionClass = $this->getErrorHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'register_shutdown_function')
            ->expects($this->any())
            ->willReturnCallback(
                function ($callable) {
                    $this->listener = $callable;
                }
            );
    }

    public function testInvokesProcessorsStackOnFatalError()
    {
        $reflectionClass = $this->getErrorHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'error_get_last')
            ->expects($this->any())
            ->willReturn([
                'type' => E_WARNING,
                'message' => 'Error message',
                'file' => 'file.php',
                'line' => 1
            ]);

        $processor1 = $this->getMockForProcessor();
        $processor1->expects($this->once())
            ->method('__invoke')
            ->willReturn($this->createMock(Exception::class));

        $processor2 = $this->getMockForProcessor();
        $processor2->expects($this->once())
            ->method('__invoke')
            ->willReturn($this->createMock(Exception::class));

        $processors = [$processor1, $processor2];

        $errorHandler = new FatalErrorHandler(10, ...$processors);
        $errorHandler->register();

        $this->triggerPHPShutdown();
    }

    public function testPassThrowableTroughStackOnFatalError()
    {
        $reflectionClass = $this->getErrorHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'error_get_last')
            ->expects($this->any())
            ->willReturn([
                'type' => E_WARNING,
                'message' => 'Error message',
                'file' => 'file.php',
                'line' => 1
            ]);

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

        $errorHandler = new FatalErrorHandler(10, ...$processors);
        $errorHandler->register();

        $this->triggerPHPShutdown();

        $this->assertSame($processor1WillReturn, $processor2WillReceive);
    }

    public function testWhenRestoredNotInvokesProcessorsStackOnFatalError()
    {
        $processor = $this->getMockForProcessor();
        $processor->expects($this->never())
            ->method('__invoke');

        $errorHandler = new FatalErrorHandler(10, $processor);
        $errorHandler->register();
        $errorHandler->restore();

        $this->triggerPHPShutdown();
    }

    protected function setErrorReporting(int $level) : int
    {
        return error_reporting($level);
    }

    protected function getErrorHandlerReflectionClass()
    {
        return new ReflectionClass(ErrorHandler::class);
    }

    protected function getMockForProcessor()
    {
        return $this->createMock(ProcessorInterface::class);
    }

    protected function triggerPHPShutdown()
    {
        ($this->listener)();
    }
}

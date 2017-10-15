<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use Exception;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

final class ErrorHandlerTest extends TestCase
{
    use PHPMock;

    /** @var Closure */
    protected $listener;

    public function setUp()
    {
        $this->setErrorReporting(E_ALL);

        $reflectionClass = $this->getErrorHandlerReflectionClass();
        $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_error_handler')
            ->expects($this->any())
            ->willReturnCallback(
                function ($callable) {
                    $this->listener = $callable;
                }
            );
    }

    public function testInvokesProcessorsStackOnError()
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

        $errorHandler = new ErrorHandler(...$processors);
        $errorHandler->register();

        $this->triggerPHPError();
    }

    public function testPassThrowableTroughStackOnError()
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

        $errorHandler = new ErrorHandler(...$processors);
        $errorHandler->register();

        $this->triggerPHPError();

        $this->assertSame($processor1WillReturn, $processor2WillReceive);
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

    protected function triggerPHPError()
    {
        ($this->listener)(E_WARNING, 'Error message', 'file.php', 1);
    }
}

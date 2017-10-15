<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;
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

    public function testPassingThrowableThroughPipeline()
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

        $errorHandler = new FatalErrorHandler(10, $processors);
        $errorHandler->register();

        $this->triggerPHPShutdown();
    }

    public function testWhenRestoredNotInvokesProcessorsStackOnFatalError()
    {
        $processor = $this->getMockForProcessor();
        $processor->expects($this->never())
            ->method('__invoke');

        $errorHandler = new FatalErrorHandler(10, [$processor]);
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

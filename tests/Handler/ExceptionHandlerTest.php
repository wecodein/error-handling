<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Handler;

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

    public function testRegistersListener()
    {
        $handler = new ExceptionHandler();

        $this->getMockForSetExceptionHandler($handler)
            ->expects($this->once());

        $handler->register();
    }

    public function testRegistersListenerOnceWithConsecutiveRegisterCalls()
    {
        $handler = new ExceptionHandler();

        $this->getMockForSetExceptionHandler($handler)
            ->expects($this->once());

        for ($i = 0; $i < 2; $i++) {
            $handler->register();
        }
    }

    public function testRestoresListener()
    {
        $handler = new ExceptionHandler();

        $this->getMockForSetExceptionHandler($handler)
            ->expects($this->any());
        $this->getMockForRestoreExceptionHandler($handler)
            ->expects($this->once());

        $handler->register();
        $handler->restore();
    }

    public function testRestoresListenerOnceWithConsecutiveRestoreCalls()
    {
        $handler = new ExceptionHandler();

        $this->getMockForSetExceptionHandler($handler)
            ->expects($this->any());
        $this->getMockForRestoreExceptionHandler($handler)
            ->expects($this->once());

        $handler->register();

        for ($i = 0; $i < 2; $i++) {
            $handler->restore();
        }
    }

    public function testPassingThrowableThroughPipeline()
    {
        error_reporting(E_ALL);

        $exceptionToReturn = new RuntimeException('New exception');

        $processors = [
            new CallableProcessor(function () use ($exceptionToReturn) {
                return $exceptionToReturn;
            }),
            new CallableProcessor(function (Throwable $returnedThrowableFromPreviousProcessor) use ($exceptionToReturn) {
                $this->assertSame($exceptionToReturn, $returnedThrowableFromPreviousProcessor);
                return $returnedThrowableFromPreviousProcessor;
            }),
        ];

        $handler = new ExceptionHandler(...$processors);

        $this->getMockForSetExceptionHandler($handler)
            ->expects($this->any())
            ->willReturnCallback(function (callable $callable) use (&$registeredExceptionHandler) {
                $registeredExceptionHandler = $callable;
            });

        $handler->register();

        $registeredExceptionHandler(new RuntimeException('New exception'));
    }

    protected function getMockForSetExceptionHandler(ExceptionHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_exception_handler');
    }

    protected function getMockForRestoreExceptionHandler(ExceptionHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'restore_exception_handler');
    }
}

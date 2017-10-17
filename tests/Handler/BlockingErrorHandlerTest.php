<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Handler;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\BlockingErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

/**
 * @runTestsInSeparateProcesses
 */
final class BlockingErrorHandlerTest extends TestCase
{
    use PHPMock;

    public function testRegistersListener()
    {
        $handler = new BlockingErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->once());

        $handler->register();
    }

    public function testRegistersListenerOnceWithConsecutiveRegisterCalls()
    {
        $handler = new BlockingErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->once());

        for ($i = 0; $i < 2; $i++) {
            $handler->register();
        }
    }

    public function testRestoresListener()
    {
        $handler = new BlockingErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->any());
        $this->getMockForRestoreErrorHandler($handler)
            ->expects($this->once());

        $handler->register();
        $handler->restore();
    }

    public function testRestoresListenerOnceWithConsecutiveRestoreCalls()
    {
        $handler = new BlockingErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->any());
        $this->getMockForRestoreErrorHandler($handler)
            ->expects($this->once());

        $handler->register();

        for ($i = 0; $i < 2; $i++) {
            $handler->restore();
        }
    }

    public function testRespectsPHPErrorReporting()
    {
        error_reporting(E_ALL & ~E_USER_ERROR);

        $processor = $this->createMock(ProcessorInterface::class);
        $processor->expects($this->never())
            ->method('__invoke');

        $handler = new BlockingErrorHandler($processor);
        $handler->setTerminateLevelMask(0);
        $handler->register();

        trigger_error('Error message', E_USER_ERROR);
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

        $handler = new BlockingErrorHandler(...$processors);
        $handler->setTerminateLevelMask(0);
        $handler->register();

        trigger_error('Error message', E_USER_ERROR);
    }

    public function testTerminatesScript()
    {
        error_reporting(E_ALL);

        $handler = new BlockingErrorHandler();
        $handler->setTerminateLevelMask(E_USER_ERROR);
        $handler->register();

        trigger_error('Error message', E_USER_ERROR);

        $this->fail('Failed asserting that handler terminated script.');
    }

    protected function getMockForSetErrorHandler(BlockingErrorHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_error_handler');
    }

    protected function getMockForRestoreErrorHandler(BlockingErrorHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'restore_error_handler');
    }
}

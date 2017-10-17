<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Handler;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

final class FatalErrorHandlerTest extends TestCase
{
    use PHPMock;

    public function testRegistersListener()
    {
        $handler = new FatalErrorHandler();

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->once());

        $handler->register();
    }

    public function testRegistersListenerOnceWithConsecutiveRegisterCalls()
    {
        $handler = new FatalErrorHandler();

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->once());

        for ($i = 0; $i < 2; $i++) {
            $handler->register();
        }
    }

    public function testRestoresListener()
    {
        $handler = new FatalErrorHandler();

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->any());

        $handler->register();
        $handler->restore();

        $this->assertTrue(true);
    }

    public function testRespectsPHPErrorReporting()
    {
        error_reporting(E_ALL & ~E_USER_ERROR);

        $processor = $this->createMock(ProcessorInterface::class);
        $processor->expects($this->never())
            ->method('__invoke');

        $handler = new FatalErrorHandler(32, $processor);

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->any())
            ->willReturnCallback(function (callable $callable) use (&$registeredShutdownFunction) {
                $registeredShutdownFunction = $callable;
            });

        $this->getMockForErrorGetLastFunction($handler)
            ->expects($this->any())
            ->willReturn([
                'type' => E_USER_ERROR,
                'message' => 'Error message',
                'file' => 'file.php',
                'line' => 1
            ]);

        $handler->register();

        // Fake shutdown
        $registeredShutdownFunction();
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

        $handler = new FatalErrorHandler(32, ...$processors);

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->any())
            ->willReturnCallback(function (callable $callable) use (&$registeredShutdownFunction) {
                $registeredShutdownFunction = $callable;
            });

        $this->getMockForErrorGetLastFunction($handler)
            ->expects($this->any())
            ->willReturn([
                'type' => E_USER_ERROR,
                'message' => 'Error message',
                'file' => 'file.php',
                'line' => 1
            ]);

        $handler->register();

        // Fake shutdown
        $registeredShutdownFunction();
    }

    public function testNotProcessingPipelineWhenRestored()
    {
        error_reporting(E_ALL);

        $processor = $this->createMock(ProcessorInterface::class);
        $processor->expects($this->never())
            ->method('__invoke');

        $handler = new FatalErrorHandler(32, $processor);

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->any())
            ->willReturnCallback(function (callable $callable) use (&$registeredShutdownFunction) {
                $registeredShutdownFunction = $callable;
            });

        $this->getMockForErrorGetLastFunction($handler)
            ->expects($this->any())
            ->willReturn([]);

        $handler->register();
        $handler->restore();

        // Fake shutdown
        $registeredShutdownFunction();
    }

    public function testNotProcessingPipelineWhenRestoredOnError()
    {
        error_reporting(E_ALL);

        $processor = $this->createMock(ProcessorInterface::class);
        $processor->expects($this->never())
            ->method('__invoke');

        $handler = new FatalErrorHandler(32, $processor);

        $this->getMockForRegisterShutdownFunction($handler)
            ->expects($this->any())
            ->willReturnCallback(function (callable $callable) use (&$registeredShutdownFunction) {
                $registeredShutdownFunction = $callable;
            });

        $this->getMockForErrorGetLastFunction($handler)
            ->expects($this->any())
            ->willReturn([
                'type' => E_USER_ERROR,
                'message' => 'Error message',
                'file' => 'file.php',
                'line' => 1
            ]);

        $handler->register();
        $handler->restore();

        // Fake shutdown
        $registeredShutdownFunction();
    }

    private function getMockForRegisterShutdownFunction(FatalErrorHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'register_shutdown_function');
    }

    private function getMockForErrorGetLastFunction(FatalErrorHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'error_get_last');
    }
}

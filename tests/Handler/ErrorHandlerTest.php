<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;
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

        $handler = new ErrorHandler($processors);
        $handler->register();

        $this->triggerPHPError();
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

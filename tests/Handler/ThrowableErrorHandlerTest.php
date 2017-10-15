<?php

namespace WeCodeIn\ErrorHandling\Handler\Tests;

use Closure;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;
use WeCodeIn\ErrorHandling\Handler\ThrowableErrorHandler;

final class ThrowableErrorHandlerTest extends TestCase
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

    public function testThrowsExceptionOnError()
    {
        $errorHandler = new ThrowableErrorHandler();
        $errorHandler->register();

        $this->expectException(Throwable::class);

        $this->triggerPHPError();
    }

    protected function setErrorReporting(int $level) : int
    {
        return error_reporting($level);
    }

    protected function getErrorHandlerReflectionClass()
    {
        return new ReflectionClass(ThrowableErrorHandler::class);
    }

    protected function triggerPHPError()
    {
        ($this->listener)(E_WARNING, 'Error message', 'file.php', 1);
    }
}

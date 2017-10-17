<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Handler;

use ErrorException;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WeCodeIn\ErrorHandling\Handler\ThrowableErrorHandler;

final class ThrowableErrorHandlerTest extends TestCase
{
    use PHPMock;

    public function testRegistersListener()
    {
        $handler = new ThrowableErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->once());

        $handler->register();
    }

    public function testRegistersListenerOnceWithConsecutiveRegisterCalls()
    {
        $handler = new ThrowableErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->once());

        for ($i = 0; $i < 2; $i++) {
            $handler->register();
        }
    }

    public function testRestoresListener()
    {
        $handler = new ThrowableErrorHandler();

        $this->getMockForSetErrorHandler($handler)
            ->expects($this->any());
        $this->getMockForRestoreErrorHandler($handler)
            ->expects($this->once());

        $handler->register();
        $handler->restore();
    }

    public function testRestoresListenerOnceWithConsecutiveRestoreCalls()
    {
        $handler = new ThrowableErrorHandler();

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

        $handler = new ThrowableErrorHandler();
        $handler->register();

        trigger_error('Error message', E_USER_ERROR);

        $this->assertTrue(true);
    }

    public function testThrowsErrorException()
    {
        error_reporting(E_ALL);

        $handler = new ThrowableErrorHandler();
        $handler->register();

        $this->expectException(ErrorException::class);

        trigger_error('Error message', E_USER_ERROR);
    }

    protected function getMockForSetErrorHandler(ThrowableErrorHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'set_error_handler');
    }

    protected function getMockForRestoreErrorHandler(ThrowableErrorHandler $handler)
    {
        $reflectionClass = new ReflectionClass($handler);
        return $this->getFunctionMock($reflectionClass->getNamespaceName(), 'restore_error_handler');
    }
}

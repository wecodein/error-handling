<?php

namespace WeCodeIn\ErrorHandling\Processor\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;

class CallableProcessorTest extends TestCase
{
    public function testInvokeReturnsThrowableProducedByCallable()
    {
        $callable = function () use (&$throwableReturnedByCallable) {
            return $throwableReturnedByCallable = new Exception();
        };

        $processor = new CallableProcessor($callable);
        $throwableReturnedByCallableProcessor = $processor(new Exception());

        $this->assertSame($throwableReturnedByCallable, $throwableReturnedByCallableProcessor);
    }
}

<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Emitter;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WeCodeIn\ErrorHandling\Tests\TestAsset\Emitter\PlainTextResponseEmitter;

class EmitterTest extends TestCase
{
    protected function setUp()
    {
        $this->setOutputCallback(function () {
        });
    }

    /**
     * @runInSeparateProcess
     */
    public function testEmittingExceptionMessage()
    {
        $emitter = new PlainTextResponseEmitter(false);

        $emitter(new RuntimeException('Something went wrong'));

        $this->assertNotEmpty($this->getActualOutput());
    }
}

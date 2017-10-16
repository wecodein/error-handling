<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Emitter;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WeCodeIn\ErrorHandling\Tests\TestAsset\Emitter\PlainTextEmitter;

class EmitterTest extends TestCase
{
    protected function setUp()
    {
        $this->setOutputCallback(function () {
        });
    }

    public function testEmittingExceptionMessage()
    {
        $emitter = new PlainTextEmitter([
            'includeTrace' => false,
        ]);

        $emitter(new RuntimeException('Something went wrong'));

        $this->assertNotEmpty($this->getActualOutput());
    }
}

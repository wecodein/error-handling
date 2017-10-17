<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Emitter;

use RuntimeException;
use WeCodeIn\ErrorHandling\Tests\TestAsset\Emitter\PlainTextResponseEmitter;

class HttpEmitterTest extends EmitterTest
{
    protected function setUp()
    {
        parent::setUp();

        $_SERVER['REQUEST_URI'] = '/test';
    }

    protected function tearDown()
    {
        parent::tearDown();

        unset($_SERVER['REQUEST_URI']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSettingHttpStatusCode()
    {
        $emitter = new PlainTextResponseEmitter(false, 502);

        $emitter(new RuntimeException('Something went wrong'));

        $this->assertSame(502, http_response_code());
    }
}

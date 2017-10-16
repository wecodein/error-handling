<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\Emitter;

use RuntimeException;
use WeCodeIn\ErrorHandling\Tests\TestAsset\Emitter\PlainTextEmitter;

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
        $emitter = new PlainTextEmitter([
            'includeTrace' => false,
        ]);

        $emitter(new RuntimeException('Something went wrong'));

        $this->assertSame(500, http_response_code());
    }
}

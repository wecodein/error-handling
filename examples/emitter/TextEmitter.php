<?php

declare(strict_types=1);

use WeCodeIn\ErrorHandling\Emitter\AbstractEmitter;

class TextEmitter extends AbstractEmitter
{
    protected function format(Throwable $throwable) : string
    {
        return static::class . ': ' . $throwable->getMessage() . PHP_EOL;
    }
}

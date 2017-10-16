<?php

use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

class BlackHoleProcessor implements ProcessorInterface
{
    public function __invoke(Throwable $throwable) : Throwable
    {
        echo self::class . ': ' . $throwable->getMessage() . PHP_EOL;
        return $throwable;
    }
}

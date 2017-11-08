<?php

use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/processor/BlackHoleProcessor.php';
require __DIR__ . '/emitter/TextEmitter.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$stack = new SplStack();
$stack->push(new TextEmitter());
$stack->push(new BlackHoleProcessor());

$handler = new ExceptionHandler(...$stack);
$handler->register();

try {
    throw new RuntimeException('New caught exception');
} catch (Throwable $throwable) {
    $handler($throwable);
}

throw new RuntimeException('New exception');

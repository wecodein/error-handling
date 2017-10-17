<?php

use WeCodeIn\ErrorHandling\Handler\ErrorHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/processor/BlackHoleProcessor.php';
require __DIR__ . '/emitter/TextEmitter.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$stack = new SplStack();
$stack->push(new TextEmitter());
$stack->push(new BlackHoleProcessor());

$handler = new ErrorHandler(...$stack);
$handler->register();

trigger_error('New error');

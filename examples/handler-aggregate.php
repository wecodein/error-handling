<?php

use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Handler\HandlerAggregate;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/emitter/TextEmitter.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$emitter = new TextEmitter();

$handler = new HandlerAggregate(
    new ErrorHandler($emitter),
    new ExceptionHandler($emitter),
    new FatalErrorHandler(16, $emitter)
);

$handler->register();

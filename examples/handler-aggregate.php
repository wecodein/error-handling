<?php

use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Handler\HandlerAggregate;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/processor/EmitterProcessor.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$processor = new EmitterProcessor();

$handler = new HandlerAggregate(
    new ErrorHandler($processor),
    new ExceptionHandler($processor),
    new FatalErrorHandler(16, $processor)
);

$handler->register();

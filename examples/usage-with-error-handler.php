<?php

use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;

require __DIR__ . '/../vendor/autoload.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$processor = new CallableProcessor(function (Throwable $throwable) : Throwable {
    // log, emmit...
    return $throwable;
});

$errorHandler = new ErrorHandler($processor);
$errorHandler->register();

$exceptionHandler = new ExceptionHandler($processor);
$exceptionHandler->register();

$fatalErrorHandler = new FatalErrorHandler(20, $processor);
$fatalErrorHandler->register();

trigger_error('Error');

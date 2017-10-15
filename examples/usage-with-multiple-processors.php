<?php

use WeCodeIn\ErrorHandling\Handler\ErrorHandler;
use WeCodeIn\ErrorHandling\Handler\ExceptionHandler;
use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;
use WeCodeIn\ErrorHandling\Processor\CallableProcessor;

require __DIR__ . '/../vendor/autoload.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$processorStack = new SplStack();
$processorStack[] = new CallableProcessor(function (Throwable $throwable) : Throwable {
    // processed second
    return $throwable;
});

$processorStack[] = new CallableProcessor(function (Throwable $throwable) : Throwable {
    // processed first;
    return $throwable;
});

$errorHandler = new ErrorHandler(...$processorStack);
$errorHandler->register();

$exceptionHandler = new ExceptionHandler(...$processorStack);
$exceptionHandler->register();

$allocateMemorySize = 20;
$fatalErrorHandler = new FatalErrorHandler($allocateMemorySize, ...$processorStack);
$fatalErrorHandler->register();

trigger_error('Error');

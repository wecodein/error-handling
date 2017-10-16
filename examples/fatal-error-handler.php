<?php

use WeCodeIn\ErrorHandling\Handler\FatalErrorHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/processor/BlackHoleProcessor.php';
require __DIR__ . '/processor/EmitterProcessor.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

ini_set('memory_limit', '4M');

error_reporting(E_ALL);

$processors = [
    new EmitterProcessor(),
    new BlackHoleProcessor()
];

$handler = new FatalErrorHandler(16, ...$processors);
$handler->register();

// Fill memory
str_repeat(' ', PHP_INT_MAX);

<?php

use WeCodeIn\ErrorHandling\Handler\BlockingErrorHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/processor/BlackHoleProcessor.php';
require __DIR__ . '/processor/EmitterProcessor.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$queue = new SplQueue();
$queue->push(new EmitterProcessor());
$queue->push(new BlackHoleProcessor());

$handler = new BlockingErrorHandler(...$queue);
$handler->register();

trigger_error('New error');

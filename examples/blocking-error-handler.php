<?php

use WeCodeIn\ErrorHandling\Handler\BlockingErrorHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/processor/BlackHoleProcessor.php';
require __DIR__ . '/emitter/TextEmitter.php';

ini_set('log_errors', 0);
ini_set('display_errors', 0);

error_reporting(E_ALL);

$queue = new SplQueue();
$queue->push(new TextEmitter());
$queue->push(new BlackHoleProcessor());

$handler = new BlockingErrorHandler(...$queue);
$handler->setTerminateLevelMask(E_USER_ERROR);
$handler->register();

trigger_error('New error', E_USER_ERROR);

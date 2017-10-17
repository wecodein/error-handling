<?php

use WeCodeIn\ErrorHandling\Handler\ThrowableErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

ini_set('log_errors', 0);
ini_set('display_errors', 1);

error_reporting(E_ALL);

$handler = new ThrowableErrorHandler();
$handler->register();

trigger_error('New error');

<?php
/**
 * This file is part of the error-handling package.
 *
 * Copyright (c) Dusan Vejin
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Handler;

use ErrorException;
use Throwable;

abstract class AbstractErrorHandler extends AbstractProcessingHandler
{
    protected function registerListener()
    {
        set_error_handler(
            function (int $type, string $message, string $file, int $line) {
                if ($this->isHandling($type)) {
                    $this->handle(new ErrorException($message, 0, $type, $file, $line));
                }
            }
        );
    }

    protected function restoreListener()
    {
        restore_error_handler();
    }

    protected function isHandling(int $level) : bool
    {
        return (bool) (error_reporting() & $level);
    }

    abstract protected function handle(Throwable $throwable);
}

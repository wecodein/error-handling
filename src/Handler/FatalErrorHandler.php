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

class FatalErrorHandler extends AbstractErrorHandler
{
    private $allocatedMemory;

    public function __construct(int $allocatedMemorySize, array $processors = [])
    {
        parent::__construct($processors);

        $this->allocatedMemory = str_repeat(' ', 1024 * $allocatedMemorySize);
    }

    protected function registerListener()
    {
        register_shutdown_function(
            function () {
                $this->allocatedMemory = null;

                if ($this->isRestored()) {
                    return;
                }

                $error = $this->getLastError();

                if ($this->isHandling($error['type'] ?? 0)) {
                    $this->handle(
                        new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
                    );
                }
            }
        );
    }

    protected function restoreListener()
    {
    }

    protected function handle(Throwable $throwable)
    {
        $this->process($throwable);
    }

    protected function getLastError() : array
    {
        return error_get_last() ?? [];
    }
}

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

final class BlockingErrorHandler extends AbstractErrorHandler
{
    protected $terminateLevelMask = E_ALL;

    public function setTerminateLevelMask(int $terminateLevelMask) : BlockingErrorHandler
    {
        $this->terminateLevelMask = $terminateLevelMask;
        return $this;
    }

    protected function handle(Throwable $throwable)
    {
        $this->process($throwable);

        if ($this->shouldTerminate($throwable)) {
            exit(1);
        }
    }

    protected function shouldTerminate(Throwable $throwable) : bool
    {
        return $throwable instanceof ErrorException &&
            $throwable->getSeverity() & $this->terminateLevelMask;
    }
}

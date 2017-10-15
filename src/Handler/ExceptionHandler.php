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

use Throwable;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

class ExceptionHandler extends AbstractHandler
{
    use ProcessableTrait;

    public function __construct(ProcessorInterface ...$processors)
    {
        $this->setProcessors(...$processors);
    }

    protected function registerListener()
    {
        set_exception_handler($this);
    }

    protected function restoreListener()
    {
        restore_exception_handler();
    }

    public function __invoke(Throwable $throwable)
    {
        $this->process($throwable);
    }
}

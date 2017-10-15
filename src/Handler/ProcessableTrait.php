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

trait ProcessableTrait
{
    /** @var ProcessorInterface[] */
    private $processors = [];

    protected function setProcessors(ProcessorInterface ...$processors)
    {
        $this->processors = $processors;
    }

    protected function process(Throwable $throwable) : Throwable
    {
        foreach ($this->processors as $processor) {
            $throwable = $processor($throwable);
        }

        return $throwable;
    }
}
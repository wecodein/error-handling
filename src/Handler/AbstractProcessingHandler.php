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

abstract class AbstractProcessingHandler extends AbstractHandler
{
    private $processors;

    public function __construct(ProcessorInterface ...$processors)
    {
        $this->processors = $processors;
    }

    final protected function process(Throwable $throwable) : Throwable
    {
        foreach ($this->processors as $processor) {
            $throwable = $processor($throwable);
        }

        return $throwable;
    }
}

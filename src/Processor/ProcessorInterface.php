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

namespace WeCodeIn\ErrorHandling\Processor;

use Throwable;

interface ProcessorInterface
{
    public function __invoke(Throwable $throwable) : Throwable;
}

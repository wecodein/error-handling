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

abstract class AbstractHandler implements HandlerInterface
{
    private $registered = false;
    private $restored = true;

    final public function register()
    {
        if (!$this->isRegistered()) {
            $this->registerListener();
            $this->registered = true;
            $this->restored = false;
        }
    }

    final public function restore()
    {
        if ($this->isRegistered() && !$this->isRestored()) {
            $this->restoreListener();
            $this->restored = true;
            $this->registered = false;
        }
    }

    final protected function isRegistered() : bool
    {
        return $this->registered;
    }

    final protected function isRestored() : bool
    {
        return $this->restored;
    }

    abstract protected function registerListener();

    abstract protected function restoreListener();
}

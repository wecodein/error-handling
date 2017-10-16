<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Emitter;

use Throwable;

abstract class AbstractHttpEmitter extends AbstractEmitter
{
    public function __invoke(Throwable $throwable) : Throwable
    {
        $this->sendHeaders();

        return parent::__invoke($throwable);
    }

    protected function sendHeaders()
    {
        if (!isset($_SERVER["REQUEST_URI"]) || headers_sent()) {
            return;
        }

        http_response_code(500);

        header("Content-Type: {$this->getContentType()}");
    }

    abstract protected function getContentType() : string;
}

<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Emitter;

use Throwable;

abstract class AbstractHttpEmitter extends AbstractEmitter
{
    /**
     * @var int
     */
    protected $httpResponseCode;

    public function __construct(bool $includeTrace = true, int $httpResponseCode = 500)
    {
        parent::__construct($includeTrace);

        $this->httpResponseCode = $httpResponseCode;
    }

    public function __invoke(Throwable $throwable) : Throwable
    {
        $this->sendHeaders();

        return parent::__invoke($throwable);
    }

    protected function sendHeaders()
    {
        if (!$this->canSendHeaders()) {
            return;
        }

        http_response_code($this->httpResponseCode);

        header("Content-Type: {$this->getContentType()}");
    }

    final protected function canSendHeaders() : bool
    {
        return !headers_sent() && isset($_SERVER["REQUEST_URI"]);
    }

    abstract protected function getContentType() : string;
}

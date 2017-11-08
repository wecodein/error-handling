<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Tests\TestAsset\Emitter;

use Throwable;
use WeCodeIn\ErrorHandling\Emitter\AbstractHttpEmitter;

final class PlainTextResponseEmitter extends AbstractHttpEmitter
{
    protected function format(Throwable $throwable) : string
    {
        $text = sprintf(
            '%s: %s',
            get_class($throwable),
            $throwable->getMessage()
        );

        if ($this->includeTrace) {
            $text .= "\n\n";

            $trace = $throwable->getTrace();
            $traceLength = count($trace);

            $text .= "Stacktrace:\n";
            foreach ($trace as $i => $traceRecord) {
                $text .= $this->formatTraceRecord($traceRecord, $i, $traceLength);
                $text .= "\n";
            }
        }

        return $text;
    }

    protected function getContentType() : string
    {
        return 'text/plain';
    }
}

<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Emitter;

use Throwable;

abstract class AbstractEmitter implements EmitterInterface
{
    /**
     * @var bool
     */
    protected $includeTrace;

    public function __construct(bool $includeTrace = true)
    {
        $this->includeTrace = $includeTrace;
    }

    public function __invoke(Throwable $throwable) : Throwable
    {
        ob_start();
        $output = $this->format($throwable);
        ob_end_clean();

        echo $output;

        return $throwable;
    }

    abstract protected function format(Throwable $throwable) : string;

    final protected function formatTraceRecord(array $traceRecord, int $index, int $traceLength) : string
    {
        return sprintf(
            '#%s %s%s%s in %s:%s',
            $traceLength - $index - 1,
            $traceRecord['class'] ?? '',
            isset($traceRecord['class'], $traceRecord['function']) ? ':' : '',
            $traceRecord['function'] ?? '',
            $traceRecord['file'] ?? 'unknown',
            $traceRecord['line'] ?? 0
        );
    }
}

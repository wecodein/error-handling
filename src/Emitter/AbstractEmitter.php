<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Emitter;

use Throwable;

abstract class AbstractEmitter implements EmitterInterface
{
    /**
     * @var array
     */
    protected $options = [
        'includeTrace' => true,
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
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

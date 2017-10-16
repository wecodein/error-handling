<?php

declare(strict_types=1);

namespace WeCodeIn\ErrorHandling\Handler;

use Throwable;
use WeCodeIn\ErrorHandling\Processor\ProcessorInterface;

abstract class AbstractProcessingHandler extends AbstractHandler
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    public function __construct(ProcessorInterface ...$processors)
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

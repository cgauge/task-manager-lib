<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager\Strategy;

use Throwable;

class ContinueOnFailure implements Strategy
{
    /** @var Throwable[] */
    private $exceptions = [];

    /**
     * @param mixed[] $context
     */
    public function execute(callable $callback, array $context = []) : void
    {
        try {
            $callback();
        } catch (Throwable $e) {
            $this->exceptions[] = $e;
        }
    }

    /**
     * @return Throwable[]
     */
    public function exceptions() : array
    {
        return $this->exceptions;
    }
}

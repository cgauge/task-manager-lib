<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager\Strategy;

class StopOnFailure implements Strategy
{
    /**
     * @param mixed[] $context
     */
    public function execute(callable $callback, array $context = []) : void
    {
        $callback();
    }
}

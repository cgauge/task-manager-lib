<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager\Strategy;

interface Strategy
{
    /**
     * @param mixed[] $context
     */
    public function execute(callable $callback, array $context = []) : void;
}

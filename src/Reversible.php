<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager;

interface Reversible
{
    /**
     * @param mixed[] $attributes
     */
    public function reverse(array $attributes) : void;
}

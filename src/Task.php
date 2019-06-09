<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager;

interface Task
{
    /**
     * @param mixed[] $attributes
     *
     * @return mixed[]
     */
    public function run(array $attributes) : array;
}

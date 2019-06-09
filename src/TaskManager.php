<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager;

use CustomerGauge\TaskManager\Strategy\StopOnFailure;
use CustomerGauge\TaskManager\Strategy\Strategy;
use function array_filter;
use function array_intersect_key;
use function array_keys;
use function array_reverse;

class TaskManager implements Task
{
    /** @var Task[] */
    private $tasks = [];

    /** @var mixed[] */
    private $attributes = [];

    /** @var Strategy */
    private $strategy;

    public function __construct(?Strategy $strategy = null)
    {
        $this->strategy = $strategy ?? new StopOnFailure();
    }

    public function add(Task $task) : self
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * @param mixed[] $attributes
     *
     * @return mixed[]
     */
    public function run(array $attributes) : array
    {
        $this->attributes = $attributes;

        foreach ($this->tasks as $task) {
            $this->strategy->execute(function () use ($task) : Task {
                $attributes = $task->run($this->attributes);

                $this->checkForDuplicatedKey($task, $attributes);

                $this->attributes += $attributes;

                return $task;
            }, $this->attributes);
        }

        return $this->attributes;
    }

    /**
     * @param mixed[] $attributes
     */
    public function reverse(array $attributes) : void
    {
        $tasks = array_reverse($this->tasks);

        $tasks = array_filter($tasks, static function ($task) : bool {
            return $task instanceof Reversible;
        });

        foreach ($this->tasks as $task) {
            $this->strategy->execute(function () use ($task, $attributes) : void {
                $task->reverse($attributes);
            }, $attributes);
        }
    }

    /**
     * @param mixed[] $attributes
     */
    private function checkForDuplicatedKey(Task $task, array $attributes) : void
    {
        $duplicated = array_intersect_key($this->attributes, $attributes);

        if ($duplicated) {
            throw InvalidTaskAttribute::duplicatedKey($task, array_keys($duplicated));
        }
    }
}

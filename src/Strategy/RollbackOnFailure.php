<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager\Strategy;

use CustomerGauge\TaskManager\Reversible;
use CustomerGauge\TaskManager\Task;
use Throwable;
use function array_filter;
use function array_unshift;

class RollbackOnFailure implements Strategy
{
    /** @var Task[] */
    private $executed = [];

    /** @var mixed[] */
    private $context;

    /**
     * @param mixed[] $context
     */
    public function execute(callable $callback, array $context = []) : void
    {
        $this->context = $context;

        try {
            $task = $callback();

            array_unshift($this->executed, $task);
        } catch (Throwable $e) {
            $this->rollback();
        }
    }

    public function rollback() : void
    {
        $tasks = array_filter($this->executed, static function ($task) : bool {
            return $task instanceof Reversible;
        });

        foreach ($tasks as $task) {
            $task->reverse($this->context);
        }
    }
}

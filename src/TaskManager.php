<?php
/*
    Task Manager Library
    Copyright (C) 2019 CustomerGauge
    foss@customergauge.com

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 3 of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with this program; if not, write to the Free Software Foundation,
    Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

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
    /** @var Task[]|Reversible[] */
    private array $tasks = [];

    /** @var mixed[] */
    private array $attributes = [];

    private Strategy $strategy;

    public function __construct(Strategy|null $strategy = null)
    {
        $this->strategy = $strategy ?? new StopOnFailure();
    }

    public function add(Task $task): self
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * @param mixed[] $attributes
     *
     * @return mixed[]
     */
    public function run(array $attributes): array
    {
        $this->attributes = $attributes;

        foreach ($this->tasks as $task) {
            $this->strategy->execute(function () use ($task): Task {
                $attributes = $task->run($this->attributes);

                $this->checkForDuplicatedKey($task, $attributes);

                $this->attributes += $attributes;

                return $task;
            }, $this->attributes);
        }

        return $this->attributes;
    }

    /** @param mixed[] $attributes */
    public function reverse(array $attributes): void
    {
        $tasks = array_reverse($this->tasks);

        $tasks = array_filter($tasks, static function ($task): bool {
            return $task instanceof Reversible;
        });

        foreach ($tasks as $task) {
            $this->strategy->execute(static function () use ($task, $attributes): void {
                $task->reverse($attributes);
            }, $attributes);
        }
    }

    /** @param mixed[] $attributes */
    private function checkForDuplicatedKey(Task $task, array $attributes): void
    {
        $duplicated = array_intersect_key($this->attributes, $attributes);

        if ($duplicated) {
            throw InvalidTaskAttribute::duplicatedKey($task, array_keys($duplicated));
        }
    }
}

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

namespace CustomerGauge\TaskManager\Strategy;

use CustomerGauge\TaskManager\Reversible;
use CustomerGauge\TaskManager\Task;
use Throwable;

use function array_filter;
use function array_unshift;

class RollbackOnFailure implements Strategy
{
    /** @var Task[] */
    private array $executed = [];

    /** @var mixed[] */
    private array $context;

    /** @param mixed[] $context */
    public function execute(callable $callback, array $context = []): void
    {
        $this->context = $context;

        try {
            $task = $callback();

            array_unshift($this->executed, $task);
        } catch (Throwable $e) {
            $this->rollback();

            throw $e;
        }
    }

    public function rollback(): void
    {
        $tasks = array_filter($this->executed, static function ($task): bool {
            return $task instanceof Reversible;
        });

        foreach ($tasks as $task) {
            try {
                $task->reverse($this->context);
            } catch (Throwable) {
            }
        }
    }
}

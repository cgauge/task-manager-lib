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

namespace Tests\CustomerGauge\TaskManager\Strategy;

use CustomerGauge\TaskManager\Strategy\StopOnFailure;
use CustomerGauge\TaskManager\Task;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

class StopOnFailureTest extends TestCase
{
    public function test_it_stops_when_a_task_fail() : void
    {
        self::expectException(Throwable::class);

        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willThrowException(new Exception());

        $createFolder->expects($this->never())
            ->method('run');

        $strategy = new StopOnFailure();

        $strategy->execute(static function () use ($createEmail, $createFolder) : void {
            $createEmail->run([]);

            $createFolder->run([]);
        });
    }
}

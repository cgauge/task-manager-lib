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

use CustomerGauge\TaskManager\Reversible;
use CustomerGauge\TaskManager\Strategy\RollbackOnFailure;
use CustomerGauge\TaskManager\Task;
use Exception;
use PHPUnit\Framework\TestCase;

class RollbackOnFailureTest extends TestCase
{
    public function test_it_rollback_when_a_task_fail(): void
    {
        $createEmail  = $this->createMock(ReversibleTask::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->expects($this->once())
            ->method('run');

        $createEmail->expects($this->once())
            ->method('reverse');

        $createFolder->method('run')
            ->willThrowException(new CreateFolderException());

        $strategy = new RollbackOnFailure();

        $strategy->execute(static function () use ($createEmail) {
            $createEmail->run([]);

            return $createEmail;
        });

        $this->expectException(CreateFolderException::class);

        $strategy->execute(static function () use ($createFolder): void {
            $createFolder->run([]);
        });
    }
}

class CreateFolderException extends Exception
{
}

interface ReversibleTask extends Task, Reversible
{
}

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

use CustomerGauge\TaskManager\Strategy\ContinueOnFailure;
use CustomerGauge\TaskManager\Task;
use Exception;
use PHPUnit\Framework\TestCase;

class ContinueOnFailureTest extends TestCase
{
    public function testItContinuesWhenATaskFail(): void
    {
        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willThrowException(new Exception());

        $createFolder->expects($this->once())
            ->method('run');

        $strategy = new ContinueOnFailure();

        $strategy->execute(static function () use ($createEmail): void {
            $createEmail->run([]);
        });

        $strategy->execute(static function () use ($createFolder): void {
            $createFolder->run([]);
        });
    }

    public function testItStoresExceptions(): void
    {
        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willThrowException(new Exception());

        $createFolder->expects($this->once())
            ->method('run');

        $strategy = new ContinueOnFailure();

        $strategy->execute(static function () use ($createEmail): void {
            $createEmail->run([]);
        });

        $strategy->execute(static function () use ($createFolder): void {
            $createFolder->run([]);
        });

        self::assertCount(1, $strategy->exceptions());
    }
}

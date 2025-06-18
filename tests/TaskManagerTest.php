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

namespace Tests\CustomerGauge\TaskManager;

use CustomerGauge\TaskManager\InvalidTaskAttribute;
use CustomerGauge\TaskManager\Reversible;
use CustomerGauge\TaskManager\Task;
use CustomerGauge\TaskManager\TaskManager;
use PHPUnit\Framework\TestCase;

class TaskManagerTest extends TestCase
{
    public function testItCanRunTasks(): void
    {
        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->expects($this->once())
            ->method('run');

        $createFolder->expects($this->once())
            ->method('run');

        $manager = new TaskManager();

        $manager->add($createEmail)
           ->add($createFolder);

        $manager->run([]);
    }

    public function testItCanManageAttributes(): void
    {
        $createEmail    = $this->createMock(Task::class);
        $createFolder   = $this->createMock(Task::class);
        $createDatabase = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willReturn(['name' => 'Jose']);

        $createFolder->method('run')
            ->willReturn(['id' => 1]);

        $createDatabase->expects($this->once())
            ->method('run')
            ->with($this->equalTo(['id' => 1, 'name' => 'Jose']));

        $manager = new TaskManager();

        $manager->add($createEmail)
           ->add($createFolder)
           ->add($createDatabase);

        $manager->run([]);
    }

    public function testTaskCanNotUseSameKeyInAttributes(): void
    {
        $this->expectException(InvalidTaskAttribute::class);

        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willReturn(['id' => 2]);

        $createFolder->method('run')
            ->willReturn(['id' => 1]);

        $manager = new TaskManager();

        $manager->add($createEmail)
           ->add($createFolder);

        $manager->run([]);
    }

    public function testTaskCanBeRevertable(): void
    {
        $createEmail  = $this->createMock(Reversible::class);
        $createFolder = $this->createMock(Reversible::class);

        $createEmail->expects($this->once())
            ->method('reverse');

        $createFolder->expects($this->once())
            ->method('reverse');

        $manager = new TaskManager();

        $manager->add($createEmail)
           ->add($createFolder);

        $manager->reverse([]);
    }
}

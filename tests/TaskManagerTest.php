<?php

declare(strict_types=1);

namespace Tests\CustomerGauge\TaskManager;

use CustomerGauge\TaskManager\InvalidTaskAttribute;
use CustomerGauge\TaskManager\Task;
use CustomerGauge\TaskManager\TaskManager;
use PHPUnit\Framework\TestCase;

class TaskManagerTest extends TestCase
{
    public function test_it_can_run_tasks() : void
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

    public function test_it_can_manage_attributes() : void
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

    public function test_task_can_not_use_same_key_in_attributes() : void
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
}

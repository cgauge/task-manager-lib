<?php

declare(strict_types=1);

namespace Tests\CustomerGauge\TaskManager\Strategy;

use CustomerGauge\TaskManager\Reversible;
use CustomerGauge\TaskManager\Strategy\RollbackOnFailure;
use CustomerGauge\TaskManager\Task;
use Exception;
use PHPUnit\Framework\TestCase;

class RollbackOnFailureTest extends TestCase
{
    public function test_it_rollback_when_a_task_fail() : void
    {
        $createEmail  = $this->createMock(ReversibleTask::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->expects($this->once())
            ->method('run');

        $createEmail->expects($this->once())
            ->method('reverse');

        $createFolder->method('run')
            ->willThrowException(new Exception());

        $strategy = new RollbackOnFailure();

        $strategy->execute(static function () use ($createEmail) {
            $createEmail->run([]);

            return $createEmail;
        });

        $strategy->execute(static function () use ($createFolder) : void {
            $createFolder->run([]);
        });
    }
}

interface ReversibleTask extends Task, Reversible
{
}

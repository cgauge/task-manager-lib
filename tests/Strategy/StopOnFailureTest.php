<?php

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

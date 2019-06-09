<?php

declare(strict_types=1);

namespace Tests\CustomerGauge\TaskManager\Strategy;

use CustomerGauge\TaskManager\Strategy\ContinueOnFailure;
use CustomerGauge\TaskManager\Task;
use Exception;
use PHPUnit\Framework\TestCase;

class ContinueOnFailureTest extends TestCase
{
    public function test_it_continues_when_a_task_fail() : void
    {
        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willThrowException(new Exception());

        $createFolder->expects($this->once())
            ->method('run');

        $strategy = new ContinueOnFailure();

        $strategy->execute(static function () use ($createEmail) : void {
            $createEmail->run([]);
        });

        $strategy->execute(static function () use ($createFolder) : void {
            $createFolder->run([]);
        });
    }

    public function test_it_stores_exceptions() : void
    {
        $createEmail  = $this->createMock(Task::class);
        $createFolder = $this->createMock(Task::class);

        $createEmail->method('run')
            ->willThrowException(new Exception());

        $createFolder->expects($this->once())
            ->method('run');

        $strategy = new ContinueOnFailure();

        $strategy->execute(static function () use ($createEmail) : void {
            $createEmail->run([]);
        });

        $strategy->execute(static function () use ($createFolder) : void {
            $createFolder->run([]);
        });

        self::assertCount(1, $strategy->exceptions());
    }
}

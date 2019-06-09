<?php

declare(strict_types=1);

namespace CustomerGauge\TaskManager;

use InvalidArgumentException;
use function get_class;
use function implode;
use function sprintf;

class InvalidTaskAttribute extends InvalidArgumentException
{
    /**
     * @param array<int, int|string> $keys
     */
    public static function duplicatedKey(Task $task, array $keys) : self
    {
        $message = sprintf(
            'Duplicate task [%s] attribute keys [%s]. Use a different attribute key.',
            get_class($task),
            implode(', ', $keys)
        );

        return new static($message);
    }
}

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

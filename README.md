[![Build Status](https://travis-ci.org/cgauge/task-manager-lib.svg?branch=master)](https://travis-ci.org/cgauge/task-manager-lib)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cgauge/task-manager-lib/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cgauge/task-manager-lib/?branch=master)

# Task Manager ⚙️

# Installation

```bash
composer require customergauge/task-manager
```
# Usage

## Execute a task

```php
use Customergauge\TaskManager\Task;
use Customergauge\TaskManager\TaskManager;

class SimpleTask implements Task
{
    public function run(array $attributes) : array
    {
        echo "simple task";
    }
}

$taskManager = new TaskManager; // defaults to StopOnFailure strategy
$taskManager->add(new SimpleTask);

$taskManager->run([]);

// output: simple task 
```
## Continue on failure strategy

```php
use Customergauge\TaskManager\Task;
use Customergauge\TaskManager\TaskManager;
use Customergauge\TaskManager\Strategy\ContinueOnFailure;

class FirstTask implements Task
{
    public function run(array $attributes) : array
    {
        throw new Exception;
    }
}

class SecondTask implements Task
{
    public function run(array $attributes) : array
    {
        echo "second task";
    }
}

$taskManager = new TaskManager(new ContinueOnFailure);
$taskManager->add(new FirstTask)
    ->add(new SecondTask)

$taskManager->run([]);

// output: second task
```

## Rollback executed tasks 

```php
use Customergauge\TaskManager\Task;
use Customergauge\TaskManager\Reversible;
use Customergauge\TaskManager\TaskManager;
use Customergauge\TaskManager\Strategy\RollbackOnFailure;

class FirstTask implements Task, Reversible
{
    public function run(array $attributes) : array
    {
        echo "first task";
    }

    public function reverse(array $attributes)
    {
        echo "reverse first task";
    }
}

class SecondTask implements Task
{
    public function run(array $attributes) : array
    {
        throw new Exception;
    }
}

$taskManager = new TaskManager(new RollbackOnFailure);
$taskManager->add(new FirstTask)
    ->add(new SecondTask)

$taskManager->run([]);

/* 
output: 
    firt task
    reverse first task
*/
```

# Contributing

Contributions are always welcome, please have a look at our issues to see if there's something you could help with.

# License

Task Manager is licensed under MIT license.

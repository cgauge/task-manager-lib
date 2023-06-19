# Task Manager ⚙️

# Installation

```bash
composer require customergauge/task-manager
```
# Usage

## Execute a task

```php
use CustomerGauge\TaskManager\Task;
use CustomerGauge\TaskManager\TaskManager;

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
use CustomerGauge\TaskManager\Task;
use CustomerGauge\TaskManager\TaskManager;
use CustomerGauge\TaskManager\Strategy\ContinueOnFailure;

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
    ->add(new SecondTask);

$taskManager->run([]);

// output: second task
```

## Rollback executed tasks 

```php
use CustomerGauge\TaskManager\Task;
use CustomerGauge\TaskManager\Reversible;
use CustomerGauge\TaskManager\TaskManager;
use CustomerGauge\TaskManager\Strategy\RollbackOnFailure;

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
    ->add(new SecondTask);

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

Task Manager Library is licensed under LGPLv3 license.

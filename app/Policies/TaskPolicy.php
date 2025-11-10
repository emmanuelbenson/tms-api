<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return true; // all authenticated users can see their tasks
    }

    public function view(User $user, Task $task)
    {
        return $user->id === $task->user_id;
    }

    public function create(User $user)
    {
        return true; // any authenticated user can create tasks
    }

    public function update(User $user, Task $task)
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task)
    {
        return $user->id === $task->user_id;
    }
}

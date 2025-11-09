<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends BaseController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $taskQuery = $user->tasks()->latest();

        // Optional filtering
        if($search = $request->query('search')) {
            $taskQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if($status = $request->query('status')) {
            $taskQuery->where('status', $status);
        }

        // Filter by date range
        if($startDate = $request->query('start_date')) {
            $taskQuery->whereDate('created_at', '>=', $startDate);
        }
        if($endDate = $request->query('end_date')) {
            $taskQuery->whereDate('created_at', '<=', $endDate);
        }

        // Paginate
        $perPage = $request->query('per_page', 5);
        $tasks = $taskQuery->paginate($perPage)->append($request->query());
        return response()->json($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201);
    }

    /**
     * Update the specified task.
     * @throws AuthorizationException
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return response()->json($task, 200);
    }

    /**
     * Remove the specified task.
     * @throws AuthorizationException
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}

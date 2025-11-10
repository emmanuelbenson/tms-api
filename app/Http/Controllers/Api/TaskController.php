<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');  // make sure users are authenticated
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $taskQuery = $user->tasks()->latest();

        // --- Optional search filter ---
        if ($search = $request->query('search')) {
            $taskQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // --- Filter by status ---
        if ($status = $request->query('status')) {
            $taskQuery->where('status', $status);
        }

        // --- Filter by date range ---
        if ($startDate = $request->query('start_date')) {
            $taskQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->query('end_date')) {
            $taskQuery->whereDate('created_at', '<=', $endDate);
        }

        // --- Sorting ---
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $taskQuery->orderBy($sortBy, $sortOrder);

        // --- Pagination ---
        $perPage = (int) $request->query('per_page', 3);
        $tasks = $taskQuery->paginate($perPage)->appends($request->query());

        // --- Return JSON response safely ---
        return response()->json($tasks, 200);
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201);
    }


    /**
     * @throws AuthorizationException
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:pending,in-progress,completed',
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

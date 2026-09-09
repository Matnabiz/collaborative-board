<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    /**
     * List sticky notes according to the selected time range.
     *
     * GET /api/tasks?range=today
     * GET /api/tasks?range=week
     * GET /api/tasks?range=month
     * GET /api/tasks?range=all
     */
    public function index(Request $request): JsonResponse
    {
        $range = $request->query('range', 'today');

        if (!in_array($range, ['today', 'week', 'month', 'all'])) {
            return response()->json([
                'message' => 'Invalid range. Use today, week, month, or all.',
            ], 422);
        }

        $query = Task::with('user:id,name,color');

        switch ($range) {
            case 'today':
                $query->whereDate(
                    'board_date',
                    Carbon::today()
                );
                break;

            case 'week':
                $query->where(
                    'board_date',
                    '>=',
                    Carbon::today()->subDays(6)->startOfDay()
                );
                break;

            case 'month':
                $query->where(
                    'board_date',
                    '>=',
                    Carbon::today()->subDays(29)->startOfDay()
                );
                break;

            case 'all':
                // No date restriction.
                break;
        }

        $tasks = $query
            ->orderBy('board_date')
            ->orderBy('created_at')
            ->get()
            ->map(function ($task) {
                if (!is_array($task->items)) {
                    $task->items = [];
                }

                return $task;
            });

        return response()->json($tasks);
    }

    /**
     * Stick a new note on the board.
     * POST /api/tasks
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:500',
            'color' => 'nullable|string|max:20',
            'pos_x' => 'nullable|numeric',
            'pos_y' => 'nullable|numeric',
            'rotation' => 'nullable|numeric|between:-25,25',
            'board_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = Task::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'items' => $request->items ?? [],
            'color' => $request->color ?? $request->user()->color,
            'pos_x' => $request->pos_x ?? rand(40, 640),
            'pos_y' => $request->pos_y ?? rand(40, 380),
            'rotation' => $request->rotation ?? rand(-6, 6),
            'board_date' => $request->board_date ?? Carbon::today(),
        ]);

        $task->load('user:id,name,color');

        return response()->json($task, 201);
    }

    /**
     * Move, recolor, or edit a note. Only the author may do this.
     * PATCH /api/tasks/{task}
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You can only edit your own notes'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|string|max:500',
            'color' => 'sometimes|string|max:20',
            'pos_x' => 'sometimes|numeric',
            'pos_y' => 'sometimes|numeric',
            'rotation' => 'sometimes|numeric|between:-25,25',
            'items' => 'sometimes|array|max:30',
            'items.*.id' => 'required|string|max:64',
            'items.*.text' => 'required|string|max:180',
            'items.*.done' => 'required|boolean',
            'items.*.priority' => 'nullable|in:none,low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task->update($validator->validated());
        $task->load('user:id,name,color');

        return response()->json($task);
    }

    /**
     * Peel a note off the board. Only the author may do this.
     * DELETE /api/tasks/{task}
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You can only delete your own notes'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Note removed']);
    }
}

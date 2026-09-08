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
     * List every sticky note on the board for a given day (defaults to today).
     * GET /api/tasks?date=2026-09-07
     */
    public function index(Request $request): JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $tasks = Task::with('user:id,name,color')
            ->whereDate('board_date', $date)
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiController extends Controller
{
    public function chat(
        Request $request,
        OpenAIService $openAI
    ): JsonResponse {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:4000',
            'previous_response_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $openAI->chat(
                $request->string('message')->toString(),
                $request->string('previous_response_id')->toString()
                    ?: null
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'AI request failed.',
                'error' => $e->getMessage(),
                'type' => get_class($e),
                ], 500);
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIService
{
    public function chat(string $message, ?string $previousResponseId = null): array
    {
        $payload = [
            'model' => 'gpt-5',
            'instructions' => implode("\n", [
                'You are the AI assistant inside Elemo.ir.',
                'Be helpful, clear, concise, and conversational.',
                'Answer the user directly.',
                'Do not claim to have access to the user\'s Elemo board unless board data is explicitly provided.',
            ]),
            'input' => $message,
        ];

        if ($previousResponseId) {
            $payload['previous_response_id'] = $previousResponseId;
        }

        $response = Http::withToken(config('services.openai.api_key'))
            ->acceptJson()
            ->timeout(60)
            ->post('https://api.openai.com/v1/responses', $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'OpenAI API error: ' . $response->body()
            );
        }

        $data = $response->json();

        $text = collect($data['output'] ?? [])
            ->flatMap(fn ($item) => $item['content'] ?? [])
            ->where('type', 'output_text')
            ->pluck('text')
            ->implode("\n");

        if (!$text) {
            throw new RuntimeException(
                'OpenAI returned no output text.'
            );
        }

        return [
            'message' => $text,
            'response_id' => $data['id'] ?? null,
        ];
    }
}

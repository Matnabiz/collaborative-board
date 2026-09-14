<?php

namespace App\Services;

use OpenAI\Client;

class OpenAIService
{
    private Client $client;

    public function __construct()
    {
        $this->client = \OpenAI::client(
            config('services.openai.api_key')
        );
    }

    public function chat(
        string $message,
        ?string $previousResponseId = null
    ): array {
        $parameters = [
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
            $parameters['previous_response_id'] =
                $previousResponseId;
        }

        $response = $this->client
            ->responses()
            ->create($parameters);

        return [
            'message' => $response->outputText,
            'response_id' => $response->id,
        ];
    }
}

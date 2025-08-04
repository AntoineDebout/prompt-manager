<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function testPrompt(array $messages, array $variables, array $schema, string $model = 'gpt-4o-mini', float $temperature = 0): string
    {
        try {
            $payload = [
                'model' => $model,
                'messages' => $this->processMessages($messages, $variables),
                'temperature' => $temperature,
                'response_format' => [
                    'type'  => 'json_schema',
                    'json_schema' => $schema,
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', $payload);

            if (!$response->successful()) {
                $error = $response->json('error.message') ?? $response->body();
                return response()->json(['error' => 'OpenAI API error: ' . $error]);
            }

            return $response->json('choices.0.message.content');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'appel à OpenAI', [
                'error' => $e->getMessage(),
                'model' => $model,
                'temperature' => $temperature
            ]);

            return $response->json(['error' => 'Une erreur est survenue lors de l\'appel à OpenAI']);
        }
    }

    private function replaceVariables(string $prompt, array $variables): string
    {
        foreach ($variables as $key => $value) {
            if (!is_string($value)) {
                $value = (string)$value;
            }
            $prompt = str_replace('{{' . $key . '}}', $value, $prompt);
        }
        
        return $prompt;
    }

    private function processMessages(array $messages, array $variables): array
    {
        return array_map(function ($message) use ($variables) {
            return [
                'role' => $message['role'],
                'content' => $this->replaceVariables($message['content'], $variables)
            ];
        }, $messages);
    }
}

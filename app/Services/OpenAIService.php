<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    public function testPrompt(string $prompt, array $variables, string $model = 'gpt-4o-mini', float $temperature = 0): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->replaceVariables($prompt, $variables),
                ],
            ],
            'temperature' => $temperature,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erreur lors de l\'appel à OpenAI: ' . $response->body());
        }

        return $response->json('choices.0.message.content');
    }

    private function replaceVariables(string $prompt, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $prompt = str_replace('{{' . $key . '}}', $value, $prompt);
        }
        
        return $prompt;
    }
}

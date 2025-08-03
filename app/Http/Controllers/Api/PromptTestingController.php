<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PromptTestRequest;

class PromptTestingController extends Controller
{
    public function __construct(
        private readonly OpenAIService $openAIService
    ) {
    }

    public function __invoke(PromptTestRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $result = $this->openAIService->testPrompt(
                $validated['messages'],
                $validated['variables'],
                $validated['model'],
                (float) $validated['temperature']
            );

            return response()->json(['result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

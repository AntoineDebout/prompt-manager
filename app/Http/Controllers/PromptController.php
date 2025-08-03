<?php
namespace App\Http\Controllers;

use App\Services\GetPromptService;
use App\Services\GetJsonSchemaService;
use Inertia\Inertia;

class PromptController extends Controller
{
    public function __construct(
        private readonly GetPromptService $getPromptService,
        private readonly GetJsonSchemaService $getJsonSchemaService
    ){
    }

    public function index()
    {
        return Inertia::render('Prompt/Index', [
            'templatePrompts' => $this->getPromptService->getPromptsOrderedBySlug(),
            'defaultSchema' => $this->getJsonSchemaService->getDefaultSchema(),
        ]);
    }
}
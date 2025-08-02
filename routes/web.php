<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PromptController;

Route::get('/', [PromptController::class, 'index'])->name('prompts.index');

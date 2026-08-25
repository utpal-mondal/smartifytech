<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;

Route::middleware('web')->post('/chat/message', [AiController::class, 'chat'])->name('chat.message');
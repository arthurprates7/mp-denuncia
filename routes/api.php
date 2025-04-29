<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GptController;

Route::post('login', [UserController::class, 'login']);

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/gpt/stream-quota', [GptController::class, 'streamQuota'])->name('gpt.streamQuota');
    Route::get('/gpt/stream-partes', [GptController::class, 'streamPartes'])->name('gpt.streamPartes');
    Route::get('/gpt/stream-fatos', [GptController::class, 'streamFatos'])->name('gpt.streamFatos');
});



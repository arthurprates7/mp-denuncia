<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GptController;
use App\Http\Controllers\ProcessoController;


Auth::routes();


Route::get('/', function () {
    return view('home');
})->name('home');



Route::get('/gpt/pdf', [GptController::class, 'getPdf'])->name('gpt.getPdf');
Route::get('/gpt/stream-quota', [GptController::class, 'streamQuota'])->name('gpt.streamQuota');
Route::get('/gpt/stream-partes', [GptController::class, 'streamPartes'])->name('gpt.streamPartes');
Route::get('/gpt/stream-fatos', [GptController::class, 'streamFatos'])->name('gpt.streamFatos');

Route::get('/processos/buscar-cnj', [ProcessoController::class, 'buscarPorCnj'])->name('processos.buscar-cnj');
Route::resource('processos', ProcessoController::class);
Route::get('processos/{processo}/download', [ProcessoController::class, 'download'])
    ->name('processos.download');

<?php

use App\Http\Controllers\MemoriaController;
use Illuminate\Support\Facades\Route;

Route::get('/memoria', [MemoriaController::class, 'index'])->name('memoria.index');

Route::get('/memoria/jugar/{nivel}', [MemoriaController::class, 'jugar'])->name('memoria.jugar');

Route::post('/memoria/guardar', [MemoriaController::class, 'guardar'])->name('memoria.guardar');

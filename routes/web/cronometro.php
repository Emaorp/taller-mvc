<?php

use App\Http\Controllers\CronometroController;
use Illuminate\Support\Facades\Route;

Route::get('/cronometro', [CronometroController::class, 'index'])->name('cronometro.index');

Route::post('/cronometro/store', [CronometroController::class, 'store'])->name('cronometro.store');

Route::delete('/cronometro/delete/{id}', [CronometroController::class, 'delete'])->name('cronometro.delete');

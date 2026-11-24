<?php

use App\Http\Controllers\PropinasController;
use Illuminate\Support\Facades\Route;

Route::get('/propinas', [PropinasController::class, 'index'])->name('propinas.index');

Route::post('/propinas/calcular', [PropinasController::class, 'calcular'])->name('propinas.calcular');

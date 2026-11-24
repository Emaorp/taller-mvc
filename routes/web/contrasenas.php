<?php

use App\Http\Controllers\ContrasenasController;
use Illuminate\Support\Facades\Route;

Route::get('/contrasenas', [ContrasenasController::class, 'index'])->name('contrasenas.index');

Route::post('/contrasenas/generar', [ContrasenasController::class, 'generar'])->name('contrasenas.generar');

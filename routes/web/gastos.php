<?php

use App\Http\Controllers\GastosController;
use Illuminate\Support\Facades\Route;

Route::get('/gastos', [GastosController::class, 'index'])->name('gastos.index');

Route::get('/gastos/resumen', [GastosController::class, 'resumen'])->name('gastos.resumen');

Route::get('/gastos/create', [GastosController::class, 'create'])->name('gastos.create');

Route::get('/gastos/edit/{id}', [GastosController::class, 'edit'])->name('gastos.edit');

Route::post('/gastos/store', [GastosController::class, 'store'])->name('gastos.store');

Route::put('/gastos/update', [GastosController::class, 'update'])->name('gastos.update');

Route::delete('/gastos/delete/{id}', [GastosController::class, 'delete'])->name('gastos.delete');

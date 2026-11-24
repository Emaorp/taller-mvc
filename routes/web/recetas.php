<?php

use App\Http\Controllers\RecetasController;
use Illuminate\Support\Facades\Route;

Route::get('/recetas', [RecetasController::class, 'index'])->name('recetas.index');

Route::get('/recetas/create', [RecetasController::class, 'create'])->name('recetas.create');

Route::get('/recetas/edit/{id}', [RecetasController::class, 'edit'])->name('recetas.edit');

Route::post('/recetas/store', [RecetasController::class, 'store'])->name('recetas.store');

Route::put('/recetas/update', [RecetasController::class, 'update'])->name('recetas.update');

Route::delete('/recetas/delete/{id}', [RecetasController::class, 'delete'])->name('recetas.delete');

Route::patch('/recetas/guardar/{id}', [RecetasController::class, 'guardar'])->name('recetas.guardar');

Route::get('/recetas/compartida/{token}', [RecetasController::class, 'compartida'])->name('recetas.compartida');

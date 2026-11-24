<?php

use App\Http\Controllers\NotasController;
use Illuminate\Support\Facades\Route;

Route::get('/notas', [NotasController::class, 'index'])->name('notas.index');

Route::get('/notas/create', [NotasController::class, 'create'])->name('notas.create');

Route::get('/notas/edit/{id}', [NotasController::class, 'edit'])->name('notas.edit');

Route::post('/notas/store', [NotasController::class, 'store'])->name('notas.store');

Route::put('/notas/update', [NotasController::class, 'update'])->name('notas.update');

Route::delete('/notas/delete/{id}', [NotasController::class, 'delete'])->name('notas.delete');

Route::get('/notas/categorias', [NotasController::class, 'categorias'])->name('notas.categorias');

Route::post('/notas/categorias/store', [NotasController::class, 'storeCategoria'])->name('notas.categorias.store');

Route::delete('/notas/categorias/delete/{id}', [NotasController::class, 'deleteCategoria'])->name('notas.categorias.delete');

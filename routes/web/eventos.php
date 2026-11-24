<?php

use App\Http\Controllers\EventosController;
use Illuminate\Support\Facades\Route;

Route::get('/eventos', [EventosController::class, 'index'])->name('eventos.index');

Route::get('/eventos/create', [EventosController::class, 'create'])->name('eventos.create');

Route::get('/eventos/edit/{id}', [EventosController::class, 'edit'])->name('eventos.edit');

Route::post('/eventos/store', [EventosController::class, 'store'])->name('eventos.store');

Route::put('/eventos/update', [EventosController::class, 'update'])->name('eventos.update');

Route::delete('/eventos/delete/{id}', [EventosController::class, 'delete'])->name('eventos.delete');

Route::get('/eventos/proximos', [EventosController::class, 'proximos'])->name('eventos.proximos');

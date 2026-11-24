<?php

use App\Http\Controllers\ReservasController;
use Illuminate\Support\Facades\Route;

Route::get('/reservas', [ReservasController::class, 'index'])->name('reservas.index');

Route::get('/reservas/create', [ReservasController::class, 'create'])->name('reservas.create');

Route::post('/reservas/store', [ReservasController::class, 'store'])->name('reservas.store');

Route::patch('/reservas/confirm/{id}', [ReservasController::class, 'confirm'])->name('reservas.confirm');

Route::patch('/reservas/cancel/{id}', [ReservasController::class, 'cancel'])->name('reservas.cancel');

Route::delete('/reservas/delete/{id}', [ReservasController::class, 'delete'])->name('reservas.delete');

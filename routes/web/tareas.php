<?php

use App\Http\Controllers\TareasController;
use Illuminate\Support\Facades\Route;

Route::get('/tareas', [TareasController::class, 'index'])->name('tareas.index');

Route::post('/tareas/store', [TareasController::class, 'store'])->name('tareas.store');

Route::patch('/tareas/toggle/{id}', [TareasController::class, 'toggle'])->name('tareas.toggle');

Route::delete('/tareas/delete/{id}', [TareasController::class, 'delete'])->name('tareas.delete');

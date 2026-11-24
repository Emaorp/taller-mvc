<?php

use App\Http\Controllers\EncuestasController;
use Illuminate\Support\Facades\Route;

Route::get('/encuestas', [EncuestasController::class, 'index'])->name('encuestas.index');

Route::get('/encuestas/create', [EncuestasController::class, 'create'])->name('encuestas.create');

Route::post('/encuestas/store', [EncuestasController::class, 'store'])->name('encuestas.store');

Route::patch('/encuestas/toggle/{id}', [EncuestasController::class, 'toggle'])->name('encuestas.toggle');

Route::delete('/encuestas/delete/{id}', [EncuestasController::class, 'delete'])->name('encuestas.delete');

Route::get('/encuestas/responder/{id}', [EncuestasController::class, 'responder'])->name('encuestas.responder');

Route::post('/encuestas/responder/{id}', [EncuestasController::class, 'guardarRespuestas'])->name('encuestas.responder.guardar');

Route::get('/encuestas/resultados/{id}', [EncuestasController::class, 'resultados'])->name('encuestas.resultados');

Route::get('/encuestas/resultados/{id}/csv', [EncuestasController::class, 'exportar'])->name('encuestas.exportar');

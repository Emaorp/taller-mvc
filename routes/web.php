<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home.index');

include('web/tareas.php');
include('web/propinas.php');
include('web/contrasenas.php');
include('web/gastos.php');
include('web/reservas.php');
include('web/notas.php');
include('web/eventos.php');
include('web/recetas.php');
include('web/memoria.php');
include('web/encuestas.php');
include('web/cronometro.php');

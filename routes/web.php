<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home.index');

include('web/tareas.php');
include('web/propinas.php');
include('web/contrasenas.php');

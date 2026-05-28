<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CamionController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\AsignacionController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:web'])->group(function () {
    Route::get('/',[HomeController::class,'index'])->name('home');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::get('/camiones', [CamionController::class, 'index'])->name('camiones');
    Route::get('/rutas', [RutaController::class, 'index'])->name('rutas');
    Route::get('/rutas/zonas', [RutaController::class, 'zonas'])->name('rutas.zonas');
    Route::get('/rutas/lista', [RutaController::class, 'rutaLista'])->name('rutas.lista');
    Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones');
});
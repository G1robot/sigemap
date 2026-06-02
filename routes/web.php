<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CamionController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\BotaderoController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\ContingenciaController;
use App\Http\Controllers\ReporteController;

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
    Route::get('/botaderos', [BotaderoController::class, 'index'])->name('botaderos');
    Route::get('/planillas', [PlanillaController::class, 'index'])->name('planillas');
    Route::get('/contingencias', [ContingenciaController::class, 'index'])->name('contingencias');
    Route::get('/rutas/gestor', [RutaController::class, 'gestorRutas'])->name('rutas.gestor');

    // Rutas para reportes
    Route::get('/reportes/dashboard', [ReporteController::class, 'dashboard'])->name('reportes.dashboard');
    Route::get('/reportes/camiones', [ReporteController::class, 'reporteCamiones'])->name('reportes.camiones');
    Route::get('/reportes/financiero', [ReporteController::class, 'reporteFinanciero'])->name('reportes.financiero');
});
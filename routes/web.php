<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Gestora\ConsultaController;
use App\Http\Controllers\Medico\MedicoConsultaController;
use Illuminate\Support\Facades\Route;

// ── Landing page (pública) ──────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ── Autenticación ───────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Módulo 3 — Administrador — Dashboard Ejecutivo ──────────────────────────
Route::middleware(['auth', 'role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                  [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data',             [DashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/dashboard/kpis',             [DashboardController::class, 'getKpis'])->name('dashboard.kpis');
    Route::get('/dashboard/especialidades',   [DashboardController::class, 'getTopEspecialidades'])->name('dashboard.especialidades');
    Route::get('/dashboard/seguros',          [DashboardController::class, 'getSeguros'])->name('dashboard.seguros');
    Route::get('/dashboard/alertas',          [DashboardController::class, 'getAlertas'])->name('dashboard.alertas');
    Route::get('/dashboard/exportar',         [DashboardController::class, 'exportar'])->name('dashboard.exportar');
});

// ── Módulo 2 — Médico — Reportes ────────────────────────────────────────────
Route::middleware(['auth', 'role:Medico'])->prefix('medico')->name('medico.')->group(function () {
    Route::get('/panel',                                    [MedicoConsultaController::class, 'index'])->name('panel');
    Route::get('/consultas',                               [MedicoConsultaController::class, 'index'])->name('consultas');
    Route::get('/consultas/{id}/reporte',                  [MedicoConsultaController::class, 'getReporte'])->name('consultas.reporte.get');
    Route::post('/consultas/{id}/reporte',                 [MedicoConsultaController::class, 'storeReporte'])->name('consultas.reporte.store');
    Route::get('/alertas',                                 [MedicoConsultaController::class, 'alertas'])->name('alertas');
});

// ── Módulo 1 — Gestora — Registro de Consultas ──────────────────────────────
Route::middleware(['auth', 'role:Gestora'])->prefix('gestora')->name('gestora.')->group(function () {
    // Panel principal → redirige a nueva consulta
    Route::get('/panel', [ConsultaController::class, 'index'])->name('panel');

    // Perfil, pacientes, notificaciones
    Route::get('/perfil',          [ConsultaController::class, 'perfil'])->name('perfil');
    Route::put('/perfil',          [ConsultaController::class, 'updatePerfil'])->name('perfil.update');
    Route::get('/pacientes',       [ConsultaController::class, 'pacientes'])->name('pacientes');
    Route::get('/notificaciones',  [ConsultaController::class, 'notificaciones'])->name('notificaciones');

    // Wizard de consulta
    Route::prefix('consultas')->name('consultas.')->group(function () {
        Route::get('/nueva',              [ConsultaController::class, 'index'])->name('nueva');
        Route::get('/historial',          [ConsultaController::class, 'historial'])->name('historial');
        Route::post('/paso1',             [ConsultaController::class, 'storePaso1'])->name('paso1');
        Route::post('/paso1/existente',   [ConsultaController::class, 'usarExistente'])->name('existente');
        Route::post('/paso2',             [ConsultaController::class, 'storePaso2'])->name('paso2');
        Route::get('/medicos-disponibles',[ConsultaController::class, 'getMedicosDisponibles'])->name('medicos');
        Route::post('/confirmar',         [ConsultaController::class, 'confirmar'])->name('confirmar');
    });
});

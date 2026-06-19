<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// En este apartado falta agregar las rutas las cuales llevan a las tareas después de iniciar sesión o registrarse.
// Por ahora da error debido a que no existe la vista para las tareas.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::get('/tasks', fn () => view('tasks'))->name('tasks');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

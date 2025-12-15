<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectLoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimerController;
use App\Http\Controllers\UserLegacyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserLegacyController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.logins', ProjectLoginController::class)->except(['index', 'show']);
    Route::get('logins', [ProjectLoginController::class, 'index'])->name('logins.index');
    Route::resource('roles', RoleController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions');
    Route::post('modules/reorder', [ModuleController::class, 'reorder'])->name('modules.reorder');
    Route::resource('modules', ModuleController::class);
    Route::resource('tasks', TaskController::class);
    Route::get('timer', [TimerController::class, 'index'])->name('timer.index');
    Route::get('timer/status', [TimerController::class, 'status'])->name('timer.status');
    Route::post('timer/start', [TimerController::class, 'start'])->name('timer.start');
    Route::post('timer/pause', [TimerController::class, 'pause'])->name('timer.pause');
    Route::post('timer/reset', [TimerController::class, 'reset'])->name('timer.reset');
    Route::post('timer', [TimerController::class, 'store'])->name('timer.store');
});

require __DIR__.'/auth.php';

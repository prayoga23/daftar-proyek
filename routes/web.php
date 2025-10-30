<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Projects\ShowProjects;
use App\Livewire\Projects\CreateProject;
use App\Livewire\Projects\EditProject;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Dashboard kini publik tanpa autentikasi
Route::get('dashboard', [DashboardController::class, 'index']);

// Semua route berikut dibuat publik (tanpa middleware auth)
Route::get('/projects', ShowProjects::class)->name('projects.index');
Route::get('/projects/create', CreateProject::class)->name('projects.create');
Route::get('/projects/{id}/edit', EditProject::class)->name('projects.edit');

Route::redirect('settings', 'settings/profile');
Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
Volt::route('settings/password', 'settings.password')->name('settings.password');
Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

// Autentikasi dinonaktifkan: tidak memuat route auth
// require __DIR__.'/auth.php';

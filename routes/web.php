<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;



Route::middleware('installer.lock')->group(function() {
    Route::get('/install', [InstallController::class, 'showInstall'])->name('install');
    Route::get('/install/database', [InstallController::class, 'showDatabaseForm'])->name('install.database');
    Route::post('/install/database', [InstallController::class, 'configureDatabase'])->name('install.database.save');
    Route::get('/install/admin', [InstallController::class, 'showAdminForm'])->name('install.admin');
    Route::post('/install/admin', [InstallController::class, 'registerAdmin'])->name('install.admin.save');
});
Route::get('/', [PostController::class, 'publicIndex'])
    ->middleware('check.installation')
    ->name('posts.public');
    
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PostController::class, 'index'])->name('admin.dashboard');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');    
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    // Route model binding by slug
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// Public “show” (optional)

Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

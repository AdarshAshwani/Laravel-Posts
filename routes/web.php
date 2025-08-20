<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteSettingsController;



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

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::post('/profiles', [ProfileController::class, 'storeSubprofile'])->name('profiles.store');
    Route::delete('/profiles/{subprofile}', [ProfileController::class, 'destroySubprofile'])->name('profiles.destroy');
    Route::patch('/profiles/{subprofile}/default', [ProfileController::class, 'makeDefault'])->name('profiles.default');
    Route::put('/profile/social', [ProfileController::class, 'updateSocial'])->name('profile.social');
});

Route::middleware(['auth']) // add your admin gate if you have one
    ->prefix('admin/settings')->name('settings.')
    ->group(function () {
        Route::get('branding',  [SiteSettingsController::class, 'edit'])->name('branding.edit');
        Route::post('branding', [SiteSettingsController::class, 'update'])->name('branding.update');
    });
// Public “show” (optional)

Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

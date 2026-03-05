<?php

use App\Http\Controllers\Install\InstallController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────────────────────────────
// Installation Wizard  –  only accessible when NOT yet installed
// (except the finish page which is shown right after the lock is created)
// ──────────────────────────────────────────────────────────────────────
Route::middleware('not.installed')->prefix('install')->name('install.')->group(function () {
    Route::get('/',            [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database',    [InstallController::class, 'database'])->name('database');
    Route::post('/database',   [InstallController::class, 'saveDatabase'])->name('database.save');
    Route::get('/app-config',  [InstallController::class, 'appConfig'])->name('app-config');
    Route::post('/app-config', [InstallController::class, 'saveAppConfig'])->name('app-config.save');
    Route::get('/migrate',     [InstallController::class, 'migrate'])->name('migrate');
    Route::post('/migrate',    [InstallController::class, 'runMigrations'])->name('migrate.run');
    Route::get('/admin',       [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin',      [InstallController::class, 'saveAdmin'])->name('admin.save');
});

// The finish page is accessible once the lock file exists (no not.installed guard)
Route::get('/install/finish', [InstallController::class, 'finish'])->name('install.finish');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cititor\CititorController;
use App\Http\Controllers\Cititor\SupervisorController;
use App\Http\Controllers\Cititor\SupervisorCititoriController;
use App\Http\Controllers\Cititor\SupervisorRuteController;

Route::middleware(['auth'])->prefix('cititor')->name('cititor.')->group(function () {

    Route::get('/selectie-luna', [CititorController::class, 'selectieLuna'])->name('selectie-luna')->middleware('permission:cititor.lista');
    Route::get('/', [CititorController::class, 'index'])->name('index')->middleware('permission:cititor.lista');
    Route::post('/store', [CititorController::class, 'store'])->name('store')->middleware('permission:cititor.store');
    Route::get('/contor/{id_cit}', [CititorController::class, 'show'])->name('show')->middleware('permission:cititor.lista');
    Route::get('/detaliu/{id_client}/{id_cit}', [CititorController::class, 'detaliu'])->name('detaliu')->middleware('permission:cititor.lista');
    Route::get('/editeaza/{citire}', [CititorController::class, 'edit'])->name('edit')->middleware('permission:cititor.store');
    Route::post('/editeaza/{citire}', [CititorController::class, 'update'])->name('update')->middleware('permission:cititor.store');

    Route::middleware(['permission:supervisor.lista'])->prefix('supervisor')->name('supervisor.')->group(function () {

        Route::get('/', [SupervisorController::class, 'index'])->name('index');
        Route::post('/confirma-bloc', [SupervisorController::class, 'confirmaBloc'])->name('confirma-bloc')->middleware('permission:supervisor.confirma');

        // Rute fixe INAINTE de {citire}
        Route::prefix('rute')->name('rute.')->group(function () {
            Route::get('/', [SupervisorRuteController::class, 'index'])->name('index');
            Route::post('/sync', [SupervisorRuteController::class, 'sync'])->name('sync');
            Route::post('/sync-contoare', [SupervisorRuteController::class, 'syncContoare'])->name('sync-contoare');
            Route::post('/sync-contoare-async', [SupervisorRuteController::class, 'syncContoareAsync'])->name('sync-contoare-async');
            Route::get('/sync-status', [SupervisorRuteController::class, 'syncStatus'])->name('sync-status');
            Route::post('/', [SupervisorRuteController::class, 'store'])->name('store');
            Route::post('/{ruta}/toggle', [SupervisorRuteController::class, 'toggleActiva'])->name('toggle');
        });

        Route::prefix('cititori')->name('cititori.')->group(function () {
            Route::get('/', [SupervisorCititoriController::class, 'index'])->name('index');
            Route::get('/create', [SupervisorCititoriController::class, 'create'])->name('create');
            Route::post('/', [SupervisorCititoriController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [SupervisorCititoriController::class, 'edit'])->name('edit');
            Route::put('/{user}', [SupervisorCititoriController::class, 'update'])->name('update');
            Route::post('/{user}/toggle-status', [SupervisorCititoriController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/reset-parola', [SupervisorCititoriController::class, 'resetParola'])->name('reset-parola');
        });

        Route::get('/export/{luna}/{an}', [SupervisorController::class, 'export'])->name('export')->middleware('permission:supervisor.export');
        Route::get('/harta', [SupervisorController::class, 'harta'])->name('harta');
        Route::get('/statistici', [SupervisorController::class, 'statistici'])->name('statistici');

        // {citire} DUPA toate rutele fixe
        Route::get('/{citire}', [SupervisorController::class, 'show'])->name('show');
        Route::post('/{citire}/confirma', [SupervisorController::class, 'confirma'])->name('confirma')->middleware('permission:supervisor.confirma');
        Route::post('/{citire}/eroare', [SupervisorController::class, 'eroare'])->name('eroare')->middleware('permission:supervisor.confirma');
        Route::post('/{citire}/respinge', [SupervisorController::class, 'respinge'])->name('respinge')->middleware('permission:supervisor.confirma');
    });
});

// Servire poze citiri (doar supervisor)
Route::get('/cititor/foto/{path}', function($path) {
    $fullPath = storage_path('app/private/citiri/' . $path);
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath);
})->where('path', '.*')->middleware(['auth'])->name('cititor.foto');

<?php

use Illuminate\Support\Facades\Route;

// Auth routes
Auth::routes(['verify' => false]);

// Redirect dupa login
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('supervisor_citiri')) {
            return redirect('/cititor/supervisor');
        }
        if (auth()->user()->hasRole('cititor')) {
            return redirect('/cititor');
        }
    }
    return redirect('/login');
})->middleware('auth');

// Include modul cititori
require __DIR__.'/cititor.php';
Route::get('/home', function () {
    if (auth()->user()->hasRole('supervisor_citiri')) {
        return redirect('/cititor/supervisor');
    }
    return redirect('/cititor');
})->name('home')->middleware('auth');
Route::get('/pagina/{slug}', function() { return redirect('/cititor'); })->name('cms.view');

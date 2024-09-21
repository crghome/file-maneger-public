<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Site\IndexController::class, 'index'])->name('index');
Route::get('/resource', [App\Http\Controllers\Site\IndexController::class, 'resource'])->name('resource');
// Route::get('/resource', [App\Http\Controllers\Site\IndexController::class, 'resource'])->middleware('auth')->name('resource');

Route::get('/login', [App\Http\Controllers\Site\AuthController::class, 'login'])->name('login');
Route::prefix('login')->name('login.')->group(function(){
    Route::post('confirm', [App\Http\Controllers\Site\AuthController::class, 'confirm'])->name('confirm');
});

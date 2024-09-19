<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Site\IndexController::class, 'index'])->name('index');
Route::get('/resource', [App\Http\Controllers\Site\IndexController::class, 'resource'])->name('resource');

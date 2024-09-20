<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/upload-file', [App\Http\Controllers\Api\FileLoaderController::class, 'uploadFile'])->name('fileLoad');

Route::post('/get-files', [App\Http\Controllers\Api\FileLoaderController::class, 'getFiles'])->name('getFiles');
Route::post('/get-files-zip', [App\Http\Controllers\Api\FileLoaderController::class, 'getFilesZip'])->name('getFilesZip');

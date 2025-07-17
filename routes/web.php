<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use Inertia\Inertia;

Route::get('/uploads', [UploadController::class, 'index'])->name('upload.index');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');


Route::get('/', function () {
    return Inertia::render('Welcome');
});

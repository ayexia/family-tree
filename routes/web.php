<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GedcomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/upload', [GedcomController::class, 'showUploadForm'])->name('upload.form');
Route::post('/upload', [GedcomController::class, 'upload'])->name('upload');

require __DIR__.'/auth.php';

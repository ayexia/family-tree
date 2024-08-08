<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GedcomController;
use App\Http\Controllers\FamilyTreeController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('homepage');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/upload', [GedcomController::class, 'upload'])->name('upload');


Route::get('/family-tree', [FamilyTreeController:: class, 'displayFamilyTree'])->name('family.tree');
Route::get('/family-graph', [FamilyTreeController:: class, 'displayFamilyTree'])->name('family.graph');
Route::get('/test', function () {
    return view('test');
});


Route::post('/upload-image', [UploadController::class, 'uploadImage']);

require __DIR__.'/auth.php';

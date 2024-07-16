<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GedcomController;
use App\Http\Controllers\FamilyTreeController;
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

Route::post('/upload', [GedcomController::class, 'upload'])->name('upload');


Route::get('/family-tree', [FamilyTreeController:: class, 'displayFamilyTree'])->name('family.tree');
Route::get('/test', function () {
    return view('test');
});

require __DIR__.'/auth.php';

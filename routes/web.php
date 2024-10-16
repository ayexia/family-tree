<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GedcomController;
use App\Http\Controllers\FamilyTreeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/home', function () {
    return view('homepage');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [GedcomController::class, 'index'])->name('home');
    Route::get('/import', [GedcomController::class, 'showUploadForm'])->name('import.form');
    Route::post('/upload', [GedcomController::class, 'upload'])->name('upload');
    Route::get('/family-tree', [FamilyTreeController::class, 'displayFamilyTree'])->name('family.tree');
    Route::get('/family-graph', [FamilyTreeController:: class, 'displayFamilyTree'])->name('family.graph');
});

Route::get('/api/family-tree-json', [FamilyTreeController::class, 'displayFamilyTree']);
Route::get('/api/family-graph-json', [FamilyTreeController::class, 'displayFamilyTree']);

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/person/{id}/edit', [FamilyTreeController::class, 'edit'])->name('person.edit')->middleware(['auth', 'verified']);

Route::put('/person/{id}', [FamilyTreeController::class, 'updateDetails'])->name('person.update')->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/display', [FamilyTreeController::class, 'familyTreeSurname'])->name('display');
    Route::get('/member/profile/{id}', [FamilyTreeController::class, 'viewProfile'])->name('member.profile');
});

Route::post('/upload', [GedcomController::class, 'upload'])->name('upload')->middleware(['auth', 'verified']);

Route::post('/upload-image', [UploadController::class, 'uploadImage'])->middleware(['auth', 'verified']);

Route::get('/export-gedcom', [GedcomController::class, 'export'])->name('export.gedcom');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::patch('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.toggle-admin');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');
});

require __DIR__.'/auth.php';

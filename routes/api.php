<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamilyTreeController;
use App\Http\Controllers\UploadController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/family-tree-json', [FamilyTreeController::class, 'displayFamilyTree']);

Route::post('/api/upload-image', [UploadController::class, 'uploadImage']);


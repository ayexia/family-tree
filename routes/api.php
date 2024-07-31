<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamilyTreeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/family-tree-json', [FamilyTreeController::class, 'displayFamilyTree']);
Route::get('/family-graph-json', [FamilyTreeController::class, 'displayFamilyTree']);


<?php

use App\Http\Controllers\KriteriaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('kriteria', [KriteriaController::class, 'index']);
Route::post('kriteria', [KriteriaController::class, 'store']);
Route::get('kriteria/{kriteria}', [KriteriaController::class, 'show']);
Route::patch('kriteria/{kriteria}', [KriteriaController::class, 'update']);
Route::delete('kriteria/{kriteria}', [KriteriaController::class, 'destroy']);

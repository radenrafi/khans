<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SpkController;
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
Route::get('kriteria/{kriteria}', [KriteriaController::class, 'show']);

Route::get('car', [CarController::class, 'index']);
Route::get('car/{car}', [CarController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('kriteria', [KriteriaController::class, 'store']);
    Route::patch('kriteria/{kriteria}', [KriteriaController::class, 'update']);
    Route::delete('kriteria/{kriteria}', [KriteriaController::class, 'destroy']);

    Route::post('car', [CarController::class, 'store']);
    Route::patch('car/{car}', [CarController::class, 'update']);
    Route::delete('car/{car}', [CarController::class, 'destroy']);

    Route::get('algorithm', [SpkController::class, 'index']);
    Route::post('algorithm', [SpkController::class, 'algorithmAhp']);
    Route::post('algorithm/confirm', [SpkController::class, 'confirmBobot']);
    Route::get('algorithm/rank', [SpkController::class, 'algorithmMaut']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

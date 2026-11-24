<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IranyitoszamController;
use App\Http\Controllers\Api\MegyeController;
use App\Http\Controllers\Api\VarosController;
use Illuminate\Support\Facades\Route;

// Minden útvonal elé automatikusan /api kerül, pl. /api/megyek

// ---------- NYILVÁNOS (bárki lekérdezheti, nem kell token) ----------
Route::post('/login', [AuthController::class, 'login']);

Route::get('/megyek', [MegyeController::class, 'index']);
Route::get('/megyek/{megye}', [MegyeController::class, 'show']);

Route::get('/varosok', [VarosController::class, 'index']);
Route::get('/varosok/{varos}', [VarosController::class, 'show']);

Route::get('/iranyitoszamok', [IranyitoszamController::class, 'index']);
Route::get('/iranyitoszamok/{iranyitoszam}', [IranyitoszamController::class, 'show']);

// ---------- VÉDETT (csak érvényes Bearer tokennel) ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::post('/iranyitoszamok', [IranyitoszamController::class, 'store']);
    Route::put('/iranyitoszamok/{iranyitoszam}', [IranyitoszamController::class, 'update']);
    Route::delete('/iranyitoszamok/{iranyitoszam}', [IranyitoszamController::class, 'destroy']);
});

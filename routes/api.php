<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('sanctum')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'changePassword']);
});

Route::prefix('tasks')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [TaskController::class, 'create']);
    Route::put('/', [TaskController::class, 'update']);
    Route::delete('/', [TaskController::class, 'delete']);
    Route::patch('/set', [TaskController::class, 'setTask']);
    Route::get('/get', [TaskController::class, 'getAll']);
    Route::get('/get/{id}', [TaskController::class, 'getById']);
});

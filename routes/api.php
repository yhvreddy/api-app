<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\APIs\V1\AuthController;
use App\Http\Controllers\ToDo\APIs\V1\TodoController;
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

// Route::middleware('auth:sanctum')

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::resource('user', AuthController::class);
    Route::delete('user/trashed/{user}', [AuthController::class, 'delete']);
    Route::patch('user/restore/{user}', [AuthController::class, 'restore']);

    Route::resource('todo', TodoController::class);
    Route::delete('todo/trashed/{todo}', [TodoController::class, 'delete']);
    Route::patch('todo/restore/{todo}', [TodoController::class, 'restore']);
});

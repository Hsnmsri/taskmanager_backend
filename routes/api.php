<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserAuthentication;
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

// Authentication
Route::post('auth/login', [UserController::class, 'login']);
Route::post('auth/reset_password/request', [UserController::class, 'requestResetPassword']);
Route::post('auth/reset_password/reset', [UserController::class, 'resetPassword']);

// User
Route::prefix('user')->group(function () {
    Route::post('create', [UserController::class, 'create']);
    Route::post('update', [UserController::class, 'update']);
    Route::post('delete', [UserController::class, 'delete']);
    Route::post('change_password', [UserController::class, 'changePassword']);
    Route::post('change_email', [UserController::class, 'changeEmail']);
    Route::post('data', [UserController::class, 'getData']);
    Route::post('list', [UserController::class, 'getList']);
});

// Task
Route::prefix('task')->group(function () {
    Route::post('create', [TaskController::class, 'create']);
    Route::post('update', [TaskController::class, 'update']);
    Route::post('delete', [TaskController::class, 'delete']);
    Route::post('done', [TaskController::class, 'taskDone']);
    Route::post('restore', [TaskController::class, 'taskRestore']);
    Route::post('list', [TaskController::class, 'getList']);
});

// Categories
Route::prefix('category')->group(function () {
    Route::post('create', [TaskController::class, 'categoryCreate']);
    Route::post('update', [TaskController::class, 'categoryUpdate']);
    Route::post('delete', [TaskController::class, 'categoryDelete']);
    Route::post('task_list', [TaskController::class, 'categoryTaskList']);
    Route::post('list', [TaskController::class, 'categoryList']);
});

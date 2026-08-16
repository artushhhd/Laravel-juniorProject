<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/courses/{course}/like', [CourseController::class, 'toggleLike']);
    Route::post('/courses/{course}/comments', [CourseController::class, 'storeComment']);

    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::patch('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/courses', [AdminController::class, 'index']);
        Route::post('/courses/{course}/approve', [AdminController::class, 'approve']);
        Route::delete('/courses/{course}', [AdminController::class, 'destroyCourse']);
        Route::post('/courses/{course}', [AdminController::class, 'updateCourse']);

        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{user}/toggle-block', [AdminController::class, 'toggleBlock']);
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);
    });
});

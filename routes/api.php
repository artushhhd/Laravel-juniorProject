<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::apiResource('courses', CourseController::class)->only(['index', 'show', 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::apiResource('courses', CourseController::class)->only(['store', 'update']);
    Route::post('/courses/{id}/like', [CourseController::class, 'toggleLike']);
    Route::post('/courses/{id}/comment', [CourseController::class, 'storeComment']);

    Route::middleware('admin')->group(function () {
        Route::get('/admin/courses', [AdminController::class, 'index']);
        Route::post('/admin/courses/{id}/approve', [AdminController::class, 'approve']);
        Route::delete('/admin/courses/{id}', [AdminController::class, 'destroyCourse']);

        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::post('/admin/users/{id}/toggle-block', [AdminController::class, 'toggleBlock']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser']);
        Route::put('/admin/courses/{id}', [AdminController::class, 'updateCourse']);
    });
});

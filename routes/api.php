<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}', [CourseController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(\Illuminate\Http\Request $r) => $r->user());
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/courses', [CourseController::class, 'store']);
    Route::match(['put', 'patch'], '/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
    Route::post('/courses/{course}/like', [CourseController::class, 'toggleLike']);
    Route::post('/courses/{course}/comment', [CourseController::class, 'storeComment']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/courses', [AdminController::class, 'index']);
        Route::post('/courses/{course}/approve', [AdminController::class, 'approve']);
        Route::post('/courses/{course}', [AdminController::class, 'updateCourse']);
        Route::delete('/courses/{course}', [AdminController::class, 'destroyCourse']);

        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{user}/toggle-block', [AdminController::class, 'toggleBlock']);
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);
    });
});

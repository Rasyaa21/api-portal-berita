<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/details', [AuthController::class, 'detail']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('news')->group(function () {
        Route::get('/approved', [PostController::class, 'ApprovePosts']);
        Route::get('/pending-approval', [PostController::class, 'unApprovePosts']);
        Route::get('/user-news', [PostController::class, 'getUserNews']);
        Route::get('/{news_id}', [PostController::class, 'SpecificNews']);
        Route::post('/', [PostController::class, 'store']);
        Route::patch('/{news_id}', [PostController::class, 'update']);
        Route::delete('/{news_id}', [PostController::class, 'delete']);
    });

    Route::prefix('news/{news_id}/comment')->group(function () {
        Route::post('/', [CommentsController::class, 'addComment']);
        Route::delete('/{id}', [CommentsController::class, 'deleteComment']);
    });

    Route::middleware(CheckAdmin::class)->group(function () {
        Route::prefix('admin/news')->group(function () {
            Route::get('/', [AdminController::class, 'index']);
            Route::put('/{news_id}/approve', [AdminController::class, 'acceptNews']);
        });
    });
});

Route::get('/send-test-email', function () {
    Mail::raw('This is a test email.', function ($message) {
        $message->to('naufalrasya21907@gmail.com')
                ->subject('Test Email');
    });
});


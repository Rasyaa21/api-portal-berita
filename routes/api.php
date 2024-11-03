<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function() {
    //Auth Route
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/details', [AuthController::class, 'detail']);

    //News Route For User
    Route::get('/news/approved', [PostController::class, 'ApprovePosts']);
    Route::get('/news/pending-approval', [PostController::class, 'unApprovePosts']);
    Route::get('/user-news', [PostController::class, 'getUserNews']);
    Route::get('/news/{news_id}', [PostController::class, 'SpecificNews']);
    Route::post('/news', [PostController::class, 'store']);
    Route::patch('/news/{news_id}', [PostController::class, 'update']);
    Route::delete('/news/{news_id}', [PostController::class, 'delete']);

    //Comments Route For Commenting on someone post
    Route::post('/news/{news_id}/comment', [CommentsController::class, 'addComment']);
    Route::delete('/news/{news_id}/comment/{id}', [CommentsController::class, 'deleteComment']);

    //News Route For Admin
    Route::group(['middleware' => CheckAdmin::class], function(){
        Route::get('/admin/news', [AdminController::class, 'index']);
        Route::put('/admin/news/{news_id}/approve', [AdminController::class, 'acceptNews']);
    });
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;

Route::get('/',[PortfolioController::class,'index']);



Route::middleware('guest:sanctum')->group(function(){
    Route::post('/login',[UserController::class,'login'])->name('login');
    Route::post('/messages', [MessageController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
});

Route::prefix('dashboard')->middleware('auth:sanctum')->group(function(){
    // user routes
    Route::get('/user', [UserController::class, 'index']);
    Route::put('/user', [UserController::class, 'update']);
    //portfolio routes
    Route::put('/portfolio', [PortfolioController::class, 'update']);
    //hero routes
    Route::get('/hero', [HeroController::class, 'index']);
    Route::post('/hero', [HeroController::class, 'store']);
    Route::put('/hero', [HeroController::class, 'update']);
    Route::delete('/hero', [HeroController::class, 'destroy']);
    //about routes
    Route::get('/about', [AboutController::class, 'index']);
    Route::post('/about', [AboutController::class, 'store']);
    Route::put('/about', [AboutController::class, 'update']);
    Route::delete('/about', [AboutController::class, 'destroy']);
    //contact routes
    Route::get('/contact', [ContactController::class, 'index']);
    Route::post('/contact', [ContactController::class, 'store']);
    Route::put('/contact', [ContactController::class, 'update']);
    Route::delete('/contact', [ContactController::class, 'destroy']);
    //messages auth routes
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/{id}', [MessageController::class, 'show']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);
    //skills routes
    Route::apiResource('/skills',SkillController::class);
    //achievements routes
    Route::apiResource('/achievements',AchievementController::class);
    //projects routes
    Route::apiResource('/projects',ProjectController::class);
    //testimonials routes
    Route::apiResource('/testimonials',TestimonialController::class);
    //services routes
    Route::apiResource('/services',ServiceController::class);
});

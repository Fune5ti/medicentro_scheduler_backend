<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\CodeCheckController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::fallback(function () {
    return response()->json(["message" => "Page Not Found!"], 404);
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('password/email',  ForgotPasswordController::class);
Route::post('password/code/check', CodeCheckController::class);
Route::post('password/code/reset', ResetPasswordController::class);


Route::group(['middleware' => 'auth:sanctum'],function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('location', LocationController::class);
    Route::apiResource('doctor', DoctorController::class);
});

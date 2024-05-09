<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AliadoApiController;

use App\Http\Controllers\Api\AuthController;


Route::get('/userProfile', [AuthController::class, 'userProfile'])->name('userProfile');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');

Route::get('/aliados', [AliadoApiController::class, 'index'])->name('index');

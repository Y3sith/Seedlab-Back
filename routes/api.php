<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaApiController;


Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout']);


Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');




Route::post('/register', [AuthController::class, 'register'])->name('register');




Route::apiResource('empresa',EmpresaApiController::class);
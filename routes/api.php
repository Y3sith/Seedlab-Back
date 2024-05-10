<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');




Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout']);


Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');

Route::post('/register', [AuthController::class, 'register'])->name('register');


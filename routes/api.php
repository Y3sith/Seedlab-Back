<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\AliadoApiController;

use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');




Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout']);

Route::get('/empresa', [EmpresaApiController::class, 'index'])->name('index');
Route::post('/empresa', [EmpresaApiController::class, 'store'])->name('store');

Route::get('/aliado', [AliadoApiController::class, 'index'])->name('index');


Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');

Route::post('/register', [AuthController::class, 'register'])->name('register');


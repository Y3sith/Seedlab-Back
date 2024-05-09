<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AliadoApiController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\AuthController;


Route::get('/userProfile', [AuthController::class, 'userProfile'])->name('userProfile');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');


//Route::get('/empresa', [EmpresaApiController::class, 'index'])->name('index');
//Route::post('/empresa', [EmpresaApiController::class, 'store'])->name('store');

Route::get('/aliados', [AliadoApiController::class, 'index'])->name('index');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

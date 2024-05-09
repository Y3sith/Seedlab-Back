<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmpresaApiController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaApiController;


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::get('/empresa', [EmpresaApiController::class, 'index'])->name('index');
Route::post('/empresa', [EmpresaApiController::class, 'store'])->name('store');


Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');

Auth::routes();


Route::post('/register', [AuthController::class, 'register'])->name('register');




Route::apiResource('empresa',EmpresaApiController::class);
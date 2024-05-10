<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmprendedorApiController;
use App\Http\Controllers\Api\SuperAdminController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);


Route::post('/validate_email', [AuthController::class, 'validate_email'])->name('validate_email');

Auth::routes();


Route::post('/register', [AuthController::class, 'register'])->name('register');




Route::apiResource('emprendedor',EmprendedorApiController::class);
Route::apiResource('superadmin',SuperAdminController::class);
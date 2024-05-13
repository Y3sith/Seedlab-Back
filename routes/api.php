<?php

use App\Http\Controllers\Api\UbicacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmprendedorApiController;
use App\Http\Controllers\Api\AliadoApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RutaApiController;use App\Http\Controllers\Api\SuperAdminController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//Rutas de login y registro
Route::group([
'prefix' => 'auth'
], function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::post('logout', [AuthController::class, 'logout']);


//Route::get('/empresa', [EmpresaApiController::class, 'index'])->name('index');
//Route::post('/empresa', [EmpresaApiController::class, 'store'])->name('store');

Route::get('/aliado', [AliadoApiController::class, 'index'])->name('index');

Route::apiResource('/ruta',RutaApiController::class);

Route::apiResource('emprendedor',EmprendedorApiController::class);
Route::apiResource('superadmin',SuperAdminController::class);

//AuthController
Route::post('/validate_email_em', [AuthController::class, 'validate_email'])->name('validate_email');
Route::post('/register_em', [AuthController::class, 'register'])->name('register');

//UbicacionController
Route::get('/deps/all', [UbicacionController::class, 'listar_dep'])->name('listar_dep');
Route::get('/mun', [UbicacionController::class, 'listar_munxdep'])->name('listar_munxdep');






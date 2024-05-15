<?php

use App\Http\Controllers\Api\UbicacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmprendedorApiController;
use App\Http\Controllers\Api\AliadoApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\Apoyo_por_EmpresaController;
use App\Http\Controllers\Api\ActividadController;
use App\Http\Controllers\Api\RutaApiController;use App\Http\Controllers\Api\SuperAdminController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//Rutas de login y registro
Route::group([
'prefix' => 'auth'
], function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('/register_em', [AuthController::class, 'register'])->name('register');
    
});

Route::post('logout', [AuthController::class, 'logout']);

Route::get('/aliado', [AliadoApiController::class, 'index'])->name('index');

//Rutas
Route::apiResource('/ruta',RutaApiController::class)->middleware('auth:api');

//Empresa
Route::apiResource('empresa',EmpresaApiController::class);
Route::apiResource('emprendedor',EmprendedorApiController::class);
Route::apiResource('apoyoxempresa',Apoyo_por_EmpresaController::class);
Route::apiResource('superadmin',SuperAdminController::class);

Route::apiResource('actividades',ActividadController::class)->middleware('auth:api');

//Emprendedor
Route::apiResource('/emprendedor',EmprendedorApiController::class);

//Super Admin
Route::apiResource('/superadmin',SuperAdminController::class);

//AuthController
Route::post('/validate_email_em', [AuthController::class, 'validate_email'])->name('validate_email');

//UbicacionController
Route::get('/deps/all', [UbicacionController::class, 'listar_dep'])->name('listar_dep');
Route::get('/mun', [UbicacionController::class, 'listar_munxdep'])->name('listar_munxdep');






<?php

use App\Http\Controllers\Api\Contenido_por_LeccionController;
use App\Http\Controllers\Api\UbicacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmprendedorApiController;
use App\Http\Controllers\Api\AliadoApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\Apoyo_por_EmpresaController;
use App\Http\Controllers\Api\ActividadController;
use App\Http\Controllers\Api\LeccionController;
use App\Http\Controllers\Api\NivelesController;
use App\Http\Controllers\Api\AsesorApiController;
use App\Http\Controllers\Api\RutaApiController;
use App\Http\Controllers\Api\SuperAdminController;



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

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::get('/aliado', [AliadoApiController::class, 'index'])->name('index')->middleware('auth:api');

//Rutas
Route::apiResource('/ruta',RutaApiController::class)->middleware('auth:api');

//Empresa
Route::apiResource('empresa',EmpresaApiController::class)->middleware('auth:api');

//Emprendedor
Route::apiResource('/emprendedor',EmprendedorApiController::class)->middleware('auth:api');

//Super Admin
Route::apiResource('/superadmin',SuperAdminController::class)->middleware('auth:api');
Route::get('/emprendedores&empresa',[SuperAdminController::class,'ver_emprendedoresxempresa'])->middleware('auth:api');
Route::post('/personalizacion',[SuperAdminController::class,'Personalizacion_sis'])->middleware('auth:api');


//AuthController
Route::post('/validate_email_em', [AuthController::class, 'validate_email'])->name('validate_email');
    
//UbicacionController
Route::get('/deps/all', [UbicacionController::class, 'listar_dep'])->name('listar_dep');
Route::get('/mun', [UbicacionController::class, 'listar_munxdep'])->name('listar_munxdep');

//AliadoController
Route::get('/aliado', [AliadoApiController::class, 'Traeraliadosactivos'])->name('Traeraliadosactivos')->middleware('auth:api');
Route::post('/create_aliado', [AliadoApiController::class, 'crearaliado'])->name('crearaliado')->middleware('auth:api');
Route::get('/verinfoaliado', [AliadoApiController::class, 'mostrarAliado'])->name('mostrarAliado')->middleware('auth:api');
Route::put('/editaraliado', [AliadoApiController::class, 'Editaraliado'])->name('Editaraliado')->middleware('auth:api');

Route::apiResource('/actividad',ActividadController::class)->middleware('auth:api');
Route::apiResource('/leccion',LeccionController::class)->middleware('auth:api');
Route::apiResource('/nivel',NivelesController::class)->middleware('auth:api');
Route::apiResource('/contenido_por_leccion',Contenido_por_LeccionController::class)->middleware('auth:api');

Route::apiResource('/asesor', AsesorApiController::class)->middleware('auth:api');








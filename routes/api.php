<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Contenido_por_LeccionController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\EmprendedorApiController;
use App\Http\Controllers\Api\AliadoApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AsesoriasController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\Apoyo_por_EmpresaController;
use App\Http\Controllers\Api\ActividadController;
use App\Http\Controllers\Api\LeccionController;
use App\Http\Controllers\Api\NivelesController;
use App\Http\Controllers\Api\AsesorApiController;
use App\Http\Controllers\Api\RutaApiController;
use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\Api\OrientadorApiController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//Rutas de login y registro
Route::group([
    'prefix' => 'auth'
], function(){
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register_em', [AuthController::class, 'register'])->name('register');
    Route::post('/validate_email_em', [AuthController::class, 'validate_email'])->name('validate_email');
    
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

//Empresa
Route::apiResource('empresa',EmpresaApiController::class)->middleware('auth:api');

//Emprendedor
Route::apiResource('/emprendedor',EmprendedorApiController::class)->middleware('auth:api');

//Orientador
Route::post('/crearOrientador',[OrientadorApiController::class,'createOrientador'])->middleware('auth:api');

//Super Admin
Route::group([
    'prefix' =>'superadmin',
    'middleware' => 'auth:api',
],function(){
    Route::get('/emprendedores&empresa',[SuperAdminController::class,'verEmprendedoresxEmpresa']);
    Route::post('/personalizacion',[SuperAdminController::class,'personalizacionSis']);
    Route::post('/crearSuperAdmin',[SuperAdminController::class,'crearSuperAdmin']);
    Route::delete('/desactivar', [SuperAdminController::class, 'destroy']);
});
   
//UbicacionController
Route::get('/deps/all', [UbicacionController::class, 'listar_dep'])->name('listar_dep');
Route::get('/mun', [UbicacionController::class, 'listar_munxdep'])->name('listar_munxdep');

//AliadoController
Route::group([
    'prefix' => 'aliado',
    'middleware' => 'auth:api',
], function(){
    Route::get('/{status}', [AliadoApiController::class,'traerAliadosActivos'])->name('Traeraliadosactivos');
    Route::post('/create_aliado', [AliadoApiController::class, 'crearAliado'])->name('crearaliado');
    Route::get('/verinfoaliado', [AliadoApiController::class, 'mostrarAliado'])->name('mostrarAliado');
    Route::put('/editaraliado', [AliadoApiController::class, 'editarAliado'])->name('Editaraliado');
    Route::get('/mostrarAsesorAliado/{id}', [AliadoApiController::class, 'mostrarAsesorAliado'])->name('MostrarAsesorAliado');
    Route::delete('/{id}', [AliadoApiController::class, 'destroy'])->name('desactivarAliado');
});

//Rutas
Route::apiResource('/ruta',RutaApiController::class)->middleware('auth:api');
//Actividad
Route::apiResource('/actividad',ActividadController::class)->middleware('auth:api');
//Leccion
Route::apiResource('/leccion',LeccionController::class)->middleware('auth:api');
//Nivel
Route::apiResource('/nivel',NivelesController::class)->middleware('auth:api');
//Contenido_por_Leccion
Route::apiResource('/contenido_por_leccion',Contenido_por_LeccionController::class)->middleware('auth:api');

//Asesor
Route::apiResource('/asesor', AsesorApiController::class)->middleware('auth:api');
Route::get('/mostrarAsesoriasAsesor/{id}/{conHorario}', [AsesorApiController::class, 'mostrarAsesoriasAsesor']);

//asesorias
Route::group([
    'prefix' => 'asesorias',
    'middleware' =>'auth:api'
], function(){
    Route::post('/solictud_asesoria',[AsesoriasController::class,'guardarAsesoria']); //guardar asesoria - emprendedor
    Route::post('/asignar_asesoria', [AsesoriasController::class, 'asignarAsesoria'])->name('asignarasesoria'); //asignar asesoria - aliado
    Route::post('/horario_asesoria',[AsesoriasController::class, 'definirHorarioAsesoria'])->name('definirhorarioasesoria'); //asignar horario - asesor
    Route::put('/editar_asignar_asesoria',[AsesoriasController::class, 'definirHorarioAsesoria'])->name('editarasignacionasesoria'); //editar asesor - aliado
    Route::post('/mis_asesorias',[AsesoriasController::class, 'traerAsesoriasPorEmprendedor'])->name('traerAsesoriasPorEmprendedor');// ver asesorias - emprendedor
    Route::get('/asesoriaOrientador',[AsesoriasController::class, 'traerAsesoriasOrientador']); // ver asesorias - orientador
    Route::get('/mostrarAsesorias/{id}/{asignacion}', [AsesoriasController::class, 'MostrarAsesorias'])->name('MostrarAsesorias'); //ver asesorias de aliado
});














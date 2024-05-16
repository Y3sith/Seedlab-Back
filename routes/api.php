<?php

use App\Http\Controllers\Api\UbicacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmprendedorApiController;
use App\Http\Controllers\Api\AliadoApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AsesoriasController;
use App\Http\Controllers\Api\EmpresaApiController;
use App\Http\Controllers\Api\Apoyo_por_EmpresaController;
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


//Rutas
Route::apiResource('/ruta',RutaApiController::class);

//Empresa
Route::apiResource('empresa',EmpresaApiController::class);

//Emprendedor
Route::apiResource('/emprendedor',EmprendedorApiController::class);



//Super Admin
Route::get('/emprendedores&empresa',[SuperAdminController::class,'ver_emprendedoresxempresa']);
Route::post('/personalizacion',[SuperAdminController::class,'Personalizacion_sis']);


//AuthController
Route::post('/validate_email_em', [AuthController::class, 'validate_email'])->name('validate_email');

//UbicacionController
Route::get('/deps/all', [UbicacionController::class, 'listar_dep'])->name('listar_dep');
Route::get('/mun', [UbicacionController::class, 'listar_munxdep'])->name('listar_munxdep');

//AliadoController
Route::get('/aliado', [AliadoApiController::class, 'Traeraliadosactivos'])->name('Traeraliadosactivos');
Route::post('/create_aliado', [AliadoApiController::class, 'crearaliado'])->name('crearaliado');
Route::get('/mostrarAsesorAliado/{id}', [AliadoApiController::class, 'MostrarAsesorAliado'])->name('MostrarAsesorAliado');
Route::get('/verinfoaliado', [AliadoApiController::class, 'mostrarAliado'])->name('mostrarAliado');
Route::put('/editaraliado', [AliadoApiController::class, 'Editaraliado'])->name('Editaraliado');

//asesorias
Route::post('/solictud_asesoria',[AsesoriasController::class,'Guardarasesoria']);
Route::post('/asignar_asesoria', [AsesoriasController::class, 'asignarasesoria'])->name('asignarasesoria');
Route::post('/horario_asesoria',[AsesoriasController::class, 'definirhorarioasesoria'])->name('definirhorarioasesoria');
Route::put('/editar_asignar_asesoria',[AsesoriasController::class, 'editarasignacionasesoria'])->name('editarasignacionasesoria');









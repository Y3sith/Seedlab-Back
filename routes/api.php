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
use App\Http\Controllers\Api\RespuestasApiController;
use App\Models\Asesoria;


//Rutas de login y registro
Route::group([
    'prefix' => 'auth'
], function(){
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register_em', [AuthController::class, 'register'])->name('register');
    Route::post('/validate_email_em', [AuthController::class, 'validate_email'])->name('validate_email');
    Route::post('/send-reset-password', [AuthController::class, "enviarRecuperarContrasena"]);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

//Empresa
Route:group([
   'middleware' => 'auth:api'
], function(){
    Route::apiResource('/empresa',EmpresaApiController::class);
    Route::post('/createEmpresa', [EmpresaApiController::class, 'store']);
});

//Emprendedor
Route::group([
    'middelware' => 'auth:api'
], function (){
    Route::apiResource('/emprendedor',EmprendedorApiController::class);
    Route::get('userProfile/{documento}', [AuthController::class, 'userProfile']);
});

//Orientador
Route::group([
    'prefix' => 'orientador',
    'middleware' => 'auth:api'
], function(){
    Route::post('/crearOrientador',[OrientadorApiController::class,'createOrientador']);
    Route::post('/asesorias/{idAsesoria}/asignar-aliado', [OrientadorApiController::class, 'asignarAsesoriaAliado']);
    Route::get('/listaAliado', [OrientadorApiController::class,'listarAliados']);
    Route::get('/listaOrientador', [OrientadorApiController::class,'mostrarOrientadores']);
});


//Super Admin
Route::group([
    'prefix' =>'superadmin',
    'middleware' => 'auth:api',
],function(){
    Route::get('/emprendedores&empresa',[SuperAdminController::class,'verEmprendedoresxEmpresa']);
    Route::post('/personalizacion',[SuperAdminController::class,'personalizacionSis']);
    Route::post('/crearSuperAdmin',[SuperAdminController::class,'crearSuperAdmin']);
    Route::delete('/desactivar', [SuperAdminController::class, 'destroy']);
    Route::get('/averageAsesorias2024', [SuperAdminController::class, 'averageAsesorias2024']);
    Route::get('/contar-usuarios', [SuperAdminController::class, 'enumerarUsuarios']);
});

   
//UbicacionController
Route::get('/deps/all', [UbicacionController::class, 'listar_dep'])->name('listar_dep');
Route::get('/mun', [UbicacionController::class, 'listar_munxdep'])->name('listar_munxdep');

//AliadoController
Route::group([
    'prefix' => 'aliado',
    'middleware' => 'auth:api',
], function(){
    Route::get('/verinfoaliado', [AliadoApiController::class, 'mostrarAliado'])->name('mostrarAliado');
    Route::put('/editaraliado', [AliadoApiController::class, 'editarAliado'])->name('Editaraliado');
    Route::get('/mostrarAsesorAliado/{id}', [AliadoApiController::class, 'mostrarAsesorAliado'])->name('MostrarAsesorAliado');
    Route::delete('/{id}', [AliadoApiController::class, 'destroy'])->name('desactivarAliado');
    Route::post('/create_aliado', [AliadoApiController::class, 'crearAliado'])->name('crearaliado');
    Route::post('/asesoria/gestionar', [AliadoApiController::class, 'gestionarAsesoria']);
    Route::post('/editarAsesorAliado/{id}', [AliadoApiController::class,'editarAsesorAliado'])->name('EditarAsesorAliado');
    Route::get('/aliado/{status}', [AliadoApiController::class,'traerAliadosActivos'])->name('Traeraliadosactivos');
    Route::get('/dashboardAliado/{idAliado}', [AliadoApiController::class,'dashboardAliado']);
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
Route::group([
    'prefix' => 'asesor',
    'middleware' => 'auth:api'
], function(){
    Route::apiResource('/asesor', AsesorApiController::class);
    Route::get('/mostrarAsesoriasAsesor/{id}/{conHorario}', [AsesorApiController::class, 'mostrarAsesoriasAsesor']);
    Route::get('/contarAsesorias/{idAsesor}',[AsesorApiController::class,'contarAsesorias']);
    Route::get('/userProfileAsesor/{id}', [AsesorApiController::class,'userProfileAsesor'])->name('UserProfileAsesor');
});

//Asesorias
Route::group([
    'prefix' => 'asesorias',
    'middleware' =>'auth:api'
], function(){
    Route::post('/solicitud_asesoria',[AsesoriasController::class,'guardarAsesoria']);//guardar asesoria - emprendedor
    Route::post('/asignar_asesoria', [AsesoriasController::class, 'asignarAsesoria'])->name('asignarasesoria'); //asignar asesoria - aliado
    Route::post('/horario_asesoria',[AsesoriasController::class, 'definirHorarioAsesoria'])->name('definirhorarioasesoria'); //asignar horario - asesor
    Route::put('/editar_asignar_asesoria',[AsesoriasController::class, 'definirHorarioAsesoria'])->name('editarasignacionasesoria'); //editar asesor - aliado
    Route::post('/mis_asesorias',[AsesoriasController::class, 'traerAsesoriasPorEmprendedor'])->name('traerAsesoriasPorEmprendedor');// ver asesorias - emprendedor
    Route::post('/asesoriaOrientador',[AsesoriasController::class, 'traerasesoriasorientador'])->name('traerAsesoriasOrientador');; // ver asesorias - orientador
    Route::post('/{idAsesoria}/asignar-aliado', [AsesoriasController::class, 'asignarAliado']); // dar aliado a asesoria - orientador
    Route::get('/mostrarAsesorias/{id}/{asignacion}', [AsesoriasController::class, 'MostrarAsesorias'])->name('MostrarAsesorias'); //ver asesorias de aliado
    Route::get('/asesores_disponibles/{idaliado}', [AsesoriasController::class, 'listarasesoresdisponibles'])->name('listarasesoresdisponibles'); //ver asesores disponibles por aliado
});

//Respuestas formulario
Route::group([
    'prefix' => 'respuestas',
    'middleware' => 'auth:api'
], function (){
    Route::post('/guardar-respuestas', [RespuestasApiController::class, 'guardarRespuestas']);
    Route::apiResource('/respuestas',RespuestasApiController::class);
});










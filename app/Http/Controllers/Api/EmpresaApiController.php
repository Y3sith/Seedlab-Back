<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\ApoyoEmpresa;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpresaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        /*muestras las empresas*/
        if (Auth::user()->id_rol != 1) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $empresa = Empresa::paginate(5);
        return new JsonResponse($empresa->items());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar permisos de usuario
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
        }

        
        // Buscar el municipio por nombre
        $municipio = Municipio::where('nombre', $request->id_municipio)->first();

        if (!$municipio) {
            return response()->json(["message" => "Municipio no encontrado"], 404);
        }


        // Crear la empresa
        $empresa = Empresa::create([
            "nombre" => $request->empresa['nombre'],
            "documento" => $request->empresa['documento'],
            "cargo" => $request->empresa['cargo'],
            "razonSocial" => $request->empresa['razonSocial'],
            "url_pagina" => $request->empresa['url_pagina'],
            "telefono" => $request->empresa['telefono'],
            "celular" => $request->empresa['celular'],
            "direccion" => $request->empresa['direccion'],
            "correo" => $request->empresa['correo'],
            "profesion" => $request->empresa['profesion'],
            "experiencia" => $request->empresa['experiencia'],
            "funciones" => $request->empresa['funciones'],
            "id_tipo_documento" => $request->empresa['id_tipo_documento'],
            "id_municipio" => $municipio->id,
            "id_emprendedor" => $request->empresa['id_emprendedor'],
        ]);

        // Procesar apoyoEmpresa si existe
        if ($request->apoyoEmpresa) {
            ApoyoEmpresa::create([
                "nombre" => $request->apoyoEmpresa['nombre'],
                "documento" => $request->apoyoEmpresa['documento'],
                "apellido" => $request->apoyoEmpresa['apellido'],
                "cargo" => $request->apoyoEmpresa['cargo'],
                "telefono" => $request->apoyoEmpresa['telefono'],
                "celular" => $request->apoyoEmpresa['celular'],
                "email" => $request->apoyoEmpresa['email'],
                "id_tipo_documento" => $request->apoyoEmpresa['id_tipo_documento'],
                "id_empresa" => $empresa->documento,
            ]);
        }
        return response()->json(["message" => "Empresa y apoyoEmpresa creados exitosamente", "empresa" => $empresa], 200);
    }

    public function crearEmpresaconAliado(Request $data){
        $response = null;
        $statusCode = 200;

        DB::transaction(function() use($data, &$response, &$statusCode ){
        $results = DB::select('CALL crearEmpresaYApoyo(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $data['documentoEmpresa'],
                $data['nombreEmpresa'],
                $data['cargoEmpresa'],
                $data['razonSocial'],
                $data['urlPagina'],
                $data['telefonoEmpresa'],
                $data['celularEmpresa'],
                $data['direccionEmpresa'],
                $data['profesion'],
                $data['correoEmpresa'],
                $data['experiencia'],
                $data['funciones'],
                $data['idTipoDocumentoEmpresa'],
                $data['documentoApoyo'],
                $data['nombreApoyo'],
                $data['apellidoApoyo'],
                $data['cargoApoyo'],
                $data['telefonoApoyo'],
                $data['celularApoyo'],
                $data['correoApoyo'],
                $data['idTipoApoyo'],
                $data['municipio'],
                $data['id_emprendedor']
            ]);

            if(!empty($results)){
                $response = $results[0]->mensaje;
                if ($response === 'La empresa ya ha sido registrada') {
                    $statusCode = 400;
                }
            }
        });

        return response()->json(["message" => $response], $statusCode);

    }


    /**
     * Display the specified resource.
     */
    public function show($id_emprendedor)
    {
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $documento)
    {
        // edita la empresa/edita y agrega apoyos 
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        $empresa = Empresa::find($documento);

        if (!$empresa) {
            return response()->json([
                'message' => 'Empresa no encontrada'
            ], 404);
        }

        $empresa->update($request->all());


        if ($request->filled('apoyoxempresa')) {
            foreach ($request->apoyoxempresa as $apoyoData) {
                if (isset($apoyoData['documento'])) {

                    $apoyo = ApoyoEmpresa::where('documento', $apoyoData['documento'])->first();
                    if ($apoyo) {

                        $apoyo->update($apoyoData);
                    } else {

                        $nuevoApoyo = new ApoyoEmpresa($apoyoData);
                        $nuevoApoyo->id_empresa = $empresa->documento;
                        $nuevoApoyo->save();
                    }
                }
            }
        }

        return response()->json(["message" => "Empresa actualizada"], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

/**
 * creacion empresa
 *{"nombre":"Gamer Oscar",
 * "documento":"123456789",
 * "cargo":"Gerente",
 * "razonSocial":"Gamer Oscar",
 * "url_pagina":"www.gameroscar.com",
 * "telefono":"123456789",
 * "celular":"3215897631",
 * "direccion":"123456789",
 * "correo":"oscar@gmail.com",
 * "profesion":"Gamer",
 * "experiencia":"Jugar juegos",
 * "funciones":"Jugar fifa",
 * "id_tipo_documento":"1",
 * "id_municipio":"866",
 * "id_emprendedor":"1000",
 * 
 * "apoyos":[
 * {
 * "documento":"1",
 * "nombre":"Marly",
 * "apellido":"Rangel",
 * "cargo":"Diseñadora de juegos",
 * "telefono:" null,
 * "celular":"3214269607",
 * "email":"rangel@gmail.com",
 * "id_tipo_documento":"1",
 * "id_empresa":"1000",
 * }
 * ]
 * 
 * }
 * 
 * 
 */

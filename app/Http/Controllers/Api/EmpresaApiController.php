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

        // Validar datos de entrada
        $validatedData = $request->validate([
            'empresa.nombre' => 'required|string',
            'empresa.documento' => 'required|string',
            'empresa.cargo' => 'required|string',
            'empresa.razonSocial' => 'required|string',
            'empresa.url_pagina' => 'nullable|string',
            'empresa.telefono' => 'required|string|max:10',
            'empresa.celular' => 'required|string|max:13',
            'empresa.direccion' => 'required|string|',
            'empresa.correo' => 'required|string|email',
            'empresa.profesion' => 'required|string',
            'empresa.experiencia' => 'nullable|string',
            'empresa.funciones' => 'nullable|string',
            'empresa.id_tipo_documento' => 'required|integer',
            'empresa.id_municipio' => 'required|string',
            'empresa.id_emprendedor' => 'required|string',
            'apoyos.*.documento' => 'nullable|string',
            'apoyos.*.nombre' => 'nullable|string',
            'apoyos.*.apellido' => 'nullable|string',
            'apoyos.*.cargo' => 'nullable|string',
            'apoyos.*.telefono' => 'nullable|string|max:10',
            'apoyos.*.celular' => 'nullable|string|max:13',
            'apoyos.*.email' => 'nullable|string|email',
            'apoyos.*.id_tipo_documento' => 'nullable|integer',
        ]);

        // Buscar el municipio por nombre
        $nombreMunicipio = $validatedData['empresa']['id_municipio'];
        $municipio = Municipio::where('nombre', $nombreMunicipio)->first();

        if (!$municipio) {
            return response()->json(["error" => "Municipio no encontrado"], 404);
        }

        // Crear la empresa
        $empresa = Empresa::create([
            "nombre" => $validatedData['empresa']['nombre'],
            "documento" => $validatedData['empresa']['documento'],
            "cargo" => $validatedData['empresa']['cargo'],
            "razonSocial" => $validatedData['empresa']['razonSocial'],
            "url_pagina" => $validatedData['empresa']['url_pagina'],
            "telefono" => $validatedData['empresa']['telefono'],
            "celular" => $validatedData['empresa']['celular'],
            "direccion" => $validatedData['empresa']['direccion'],
            "correo" => $validatedData['empresa']['correo'],
            "profesion" => $validatedData['empresa']['profesion'],
            "experiencia" => $validatedData['empresa']['experiencia'],
            "funciones" => $validatedData['empresa']['funciones'],
            "id_tipo_documento" => $validatedData['empresa']['id_tipo_documento'],
            "id_municipio" => $municipio->id,
            "id_emprendedor" => $validatedData['empresa']['id_emprendedor'],
        ]);

        // Manejar apoyos
        $apoyos = [];
        if ($request->has('apoyos')) {
            foreach ($validatedData['apoyos'] as $apoyo) {
                $nuevoApoyo = ApoyoEmpresa::create([
                    "documento" => $apoyo['documento'],
                    "nombre" => $apoyo['nombre'],
                    "apellido" => $apoyo['apellido'],
                    "cargo" => $apoyo['cargo'],
                    "telefono" => $apoyo['telefono'],
                    "celular" => $apoyo['celular'],
                    "email" => $apoyo['email'],
                    "id_tipo_documento" => $apoyo['id_tipo_documento'],
                    "id_empresa" => $empresa->documento,
                ]);
                $apoyos[] = $nuevoApoyo;
            }
        }

        return response()->json([
            'message' => 'Empresa y apoyoEmpresa creados exitosamente',
            'empresa' => $empresa,
            'apoyos' => $apoyos
        ], 200);
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\ApoyoEmpresa;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpresaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         /*muestras las empresas*/
         if(Auth::user()->id_rol !=1){
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
    // Crear empresa
    if(Auth::user()->id_rol!=5){
        return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
    }

    $empresa= Empresa::create([
        "nombre" => $request->nombre,
        "documento" => $request->documento,
        "cargo" => $request->cargo,
        "razonSocial" => $request->razonSocial,
        "url_pagina" => $request->url_pagina,
        "telefono" => $request->telefono,
        "celular" => $request->celular,
        "direccion" => $request->direccion,
        "correo" => $request->correo,
        "profesion" => $request->profesion,
        "experiencia" => $request->experiencia,
        "funciones" => $request->funciones,
        "id_tipo_documento" => $request->id_tipo_documento,
        "id_municipio" => $request->id_municipio,
        "id_emprendedor" => $request->id_emprendedor,

    ]);
   
    if ($request->filled('apoyos')){
        $apoyos = $request->apoyos;
        foreach ($apoyos as $apoyo){
            $nuevoApoyo = new ApoyoEmpresa();
            $nuevoApoyo->documento = $apoyo['documento'];
            $nuevoApoyo->nombre = $apoyo['nombre'];
            $nuevoApoyo->apellido = $apoyo['apellido'];
            $nuevoApoyo->cargo = $apoyo['cargo'];
            $nuevoApoyo->telefono = $apoyo['telefono'];
            $nuevoApoyo->celular = $apoyo['celular'];
            $nuevoApoyo->email = $apoyo['email'];
            $nuevoApoyo->id_tipo_documento = $apoyo['id_tipo_documento'];
            $nuevoApoyo->id_empresa = $empresa->documento;
            $nuevoApoyo->save();
        }
    }
    return response()->json(['message' => 'Epresa Creada exitosamente',$empresa, 200]);
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
        if(Auth::user()->id_rol !=5){
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


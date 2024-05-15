<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
         $empresa = Empresa::paginate(5);
         return new JsonResponse($empresa->items());
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Crear empresa
    $empresa = new Empresa();
    $empresa->nombre = $request->nombre;
    $empresa->documento = $request->documento;
    $empresa->cargo = $request->cargo;
    $empresa->razonSocial = $request->razonSocial;
    $empresa->url_pagina = $request->url_pagina;
    $empresa->telefono = $request->telefono;
    $empresa->celular = $request->celular;
    $empresa->direccion = $request->direccion;
    $empresa->correo = $request->correo;
    $empresa->profesion = $request->profesion;
    $empresa->experiencia = $request->experiencia;
    $empresa->funciones = $request->funciones;
    $empresa->id_tipo_documento = $request->id_tipo_documento;
    $empresa->id_municipio = $request->id_municipio;
    $empresa->id_emprendedor = $request->id_emprendedor;
    $empresa->save();
    
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
    }else{

    }
    return response()->json($empresa, 200);
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

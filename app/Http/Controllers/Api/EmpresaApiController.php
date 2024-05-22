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
    // Verificar permisos
    if(Auth::user()->id_rol != 5){
        return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
    }

    // Obtener el nombre del departamento desde el request
    $nombreDepartamento = $request->input('nombre_departamento');

    // Buscar el departamento en la base de datos
    $departamento = Departamento::where('name', $nombreDepartamento)->first();
    if (!$departamento) {
        return response()->json(["error" => "Departamento no encontrado"], 404);
    }

    // Obtener municipios del departamento
    $municipios = Municipio::where('id_departamento', $departamento->id)->get();

    // Validar si se encontraron municipios
    if ($municipios->isEmpty()) {
        return response()->json(["error" => "No se encontraron municipios para el departamento proporcionado"], 404);
    }

    // Suponiendo que seleccionas el primer municipio encontrado para asignarlo a la empresa
    // Esto podría ajustarse según tus necesidades
    $id_municipio = $municipios->first()->id;

    // Crear empresa
    $empresa = Empresa::create([
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
        "id_municipio" => $id_municipio,
        "id_emprendedor" => $request->id_emprendedor,
    ]);

    // Manejar apoyos
    if ($request->filled('apoyos')) {
        $apoyos = $request->input('apoyos');
        foreach ($apoyos as $apoyo) {
            $nuevoApoyo = new ApoyoEmpresa();
            $nuevoApoyo->documento = $apoyo['documento'];
            $nuevoApoyo->nombre = $apoyo['nombre'];
            $nuevoApoyo->apellido = $apoyo['apellido'];
            $nuevoApoyo->cargo = $apoyo['cargo'];
            $nuevoApoyo->telefono = $apoyo['telefono'];
            $nuevoApoyo->celular = $apoyo['celular'];
            $nuevoApoyo->email = $apoyo['email'];
            $nuevoApoyo->id_tipo_documento = $apoyo['id_tipo_documento'];
            $nuevoApoyo->id_empresa = $empresa->id;
            $nuevoApoyo->save();
        }
    }

    return response()->json(['message' => 'Empresa creada exitosamente', 'empresa' => $empresa], 200);
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


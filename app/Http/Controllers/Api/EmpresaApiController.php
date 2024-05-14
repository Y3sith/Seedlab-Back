<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        //crear
        $empresa = new Empresa();
        $empresa->nombre = $request->nombre;
        $empresa->documento = $request->documento;
        $empresa->cargo = $request->cargo;
        $empresa->razonSocial = $request->razonSocial;
        $empresa->urlPagina = $request->urlPagina;
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
        //editar
        /*editar la empresa */
        $empresa = Empresa::find($documento);
        if(!$empresa){
            return response()->json([
               'message' => 'Empresa no encontrada'], 404);
        }
        else{
            $empresa->nombre = $request->nombre;
            $empresa->cargo = $request->cargo;
            $empresa->razonSocial = $request->razonSocial;
            $empresa->urlPagina = $request->urlPagina;
            $empresa->documento = $request->documento;
            $empresa->telefono = $request->telefono;
            $empresa->celular = $request->celular;
            $empresa->direccion = $request->direccion;
            $empresa->correo = $request->correo;
            $empresa->profesion = $request->profesion;
            $empresa->experiencia = $request->experiencia;
            $empresa->funciones = $request->funciones;
            $empresa->update();
            return response()->json(["message"=>"Empresa acualizada"],200);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
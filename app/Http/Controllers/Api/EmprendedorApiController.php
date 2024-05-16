<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Emprendedor;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class EmprendedorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //muestra los emprendedores - super administrator
        if(Auth::user()->id_rol =!1){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $emprendedor = Emprendedor::paginate(5);
        return new JsonResponse($emprendedor->items());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //crear emprendedor
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id_emprendedor)
    {
        /* Muestra las empresas asociadas por el emprendedor */
        if(Auth::user()->id_rol !=5){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $empresa = Empresa::where('id_emprendedor', $id_emprendedor)->paginate(5);
        if ($empresa->isEmpty()) {
            return response()->json(["error" => "Empresa no encontrada"], 404);
        }
        return response()->json($empresa->items(), 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $documento)
    {
        //editar el emprendedor
        if(Auth::user()->id_rol != 5){
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $emprendedor = Emprendedor::find($documento);
        if (!$emprendedor) {
            return response([
                'message' => 'Emprendedor no encontrado'
            ], 404);
        }
        $emprendedor->update([
            "documento" => $request->documento,
            "nombre" => $request->nombre,
            "apellido" => $request->apellido,
            "celular" => $request->celular,
            "genero" => $request->genero,
            "fecha_nac" => $request->fecha_nac,
            "direccion" => $request->direccion,
            "id_autentication" => $request->id_autentication,
            "id_tipo_documento" => $request->id_tipo_documento,
            "id_municipio" => $request->id_municipio
        ]);

        return response()->json(['message' => 'Emprendedor actualizado', $emprendedor, 200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($documento)
    {
        if(Auth::user()->id_rol != 5){
            return response()->json(["error" => "No tienes permisos para desactivar la cuenta"], 401);
        }
        $emprendedor = Emprendedor::find($documento);
        if(!$emprendedor){
            return response()->json([
               'message' => 'Emprendedor no encontrado'
            ], 404);
        }
        $emprendedor->update([
            'estado' => 0,
        ]);
        return response()->json([
           'message' => 'Emprendedor desactivado'
        ], 200);
    }
}

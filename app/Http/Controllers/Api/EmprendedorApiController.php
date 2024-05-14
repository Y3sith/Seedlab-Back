<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        //muesra los emprendedores
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
        $emprendedor = Emprendedor::create([
            'documento' => $request->documento,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'celular' => $request->celular,
            'genero' => $request->genero,
            'fecha_nac' => $request->fecha_nac,
            'direccion' => $request->direccion,
            'id_autentication' => $request->id_autentication,
            'id_tipo_documento' => $request->id_tipo_documento,
            'id_municipio' => $request->id_municipio,
        ]);
        return response()->json($emprendedor, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_emprendedor)
    {
        /* Muestra las empresas asociadas por el emprendedor */
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
        //
    }
}

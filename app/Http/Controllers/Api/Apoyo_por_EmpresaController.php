<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApoyoEmpresa;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Apoyo_por_EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //muestra los apoyos 
        $apoyoxempresa = ApoyoEmpresa::paginate(5);
        return new JsonResponse($apoyoxempresa->items());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //crear 
        $apoyoxempresa = ApoyoEmpresa::create([
            'documento' => $request->documento,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cargo' => $request->cargo,
            'telefono' => $request->telefono,
            'celular' => $request->celular,
            'email' => $request->email,
            'id_tipo_documento' => $request->id_tipo_documento,
            'id_empresa' => $request->id_empresa,
        ]);
        return response()->json($apoyoxempresa, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_empresa)
    {
        //mostrar empresa con apoyos 
        $empresa = Empresa::with('apoyoxempresa')->find($id_empresa);

        return new JsonResponse($empresa);
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
        //edita el apoyo
        $apoyoxempresa = ApoyoEmpresa::find($documento);
        if (!$apoyoxempresa) {
            return response([
                'message' => 'Apoyo no encontrado'
            ], 404);
        } else {
            $apoyoxempresa->nombre = $request->nombre;
            $apoyoxempresa->apellido = $request->apellido;
            $apoyoxempresa->cargo = $request->cargo;
            $apoyoxempresa->telefono = $request->telefono;
            $apoyoxempresa->celular = $request->celular;
            $apoyoxempresa->email = $request->email;
            $apoyoxempresa->update();
            return response()->json($apoyoxempresa, 200);
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

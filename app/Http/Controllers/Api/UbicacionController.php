<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function listar_dep()
    {
        $nombresDepartamentos = Departamento::pluck('name');
        return response()->json($nombresDepartamentos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listar_munxdep(Request $request)        
    {
        $nombreDepartamento = $request->input('dep_name');
        $departamento = Departamento::where('name', $nombreDepartamento)->first();

        if (!$departamento) {
            return response()->json(['error' => 'Departamento no encontrado'], 404);
        }

        $municipios = Municipio::where('id_departamento', $departamento->id)
            ->select('id', 'nombre')
            ->get();

        return response()->json($municipios);
    }

}

// ejemplo de usar el listar municipios por departamento
// http://127.0.0.1:8000/api/mun/?dep_name=Nariño
// en postamn llegas y le pones donde dice key: dep:name y en value el nombre del departamento

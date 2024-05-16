<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;

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
        $municipios = Municipio::where('id_departamento', $departamento->id)->pluck('nombre');
        return response()->json($municipios);
    }

}

// ejemplo de usar el listar municipios por departamento
// http://127.0.0.1:8000/api/mun/?dep_name=Nariño
// en postamn llegas y le pones donde dice key: dep:name y en value el nombre del departamento
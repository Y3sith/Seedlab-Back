<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UbicacionService;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{

    protected $ubicacionService;

    public function __construct(UbicacionService $ubicacionService)
    {
        $this->ubicacionService = $ubicacionService;
    }
    public function listar_dep()
    {
        try {
            $departamentos = $this->ubicacionService->listarDepartamentos();
            return response()->json($departamentos, 200, [], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al listar departamentos'], 500);
        }
    }
    //Funcion para traer los municipios del departamento selecionado
    public function listar_munxdep(Request $request)
    {
        try {
            $idDepartamento = intval($request->input('dep_id'));
            $municipios = $this->ubicacionService->listarMunicipiosPorDepartamento($idDepartamento);
            return response()->json($municipios, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}

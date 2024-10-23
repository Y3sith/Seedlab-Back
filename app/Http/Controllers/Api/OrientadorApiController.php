<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrientadorService;
use Illuminate\Http\Request;

class OrientadorApiController extends Controller
{
    protected $orientadorService;

    public function __construct(OrientadorService $orientadorService)
    {
        $this->orientadorService = $orientadorService;
    }

    public function createOrientador(Request $request)
    {
        $data = $request->all();
        $response = $this->orientadorService->crearOrientador($data);

        // Verifica si se ha producido un error y ajusta el código de estado
        if ($response['status'] === 200) {
            return response()->json([
                'message' => $response['mensaje'],
                'email' => $response['email']
            ], 200);
        } else {
            return response()->json([
                'message' => $response['mensaje']
            ], $response['status']);
        }
    }


    public function asignarAsesoriaAliado(Request $request, $idAsesoria)
    {
        $nombreAliado = $request->input('nombreAliado');
        $this->orientadorService->asignarAsesoriaAliado($idAsesoria, $nombreAliado);
        return response()->json(['message' => 'Aliado asignado correctamente'], 200);
    }

    public function listarAliados()
    {
        $aliados = $this->orientadorService->listarAliados();
        return response()->json($aliados, 200);
    }

    public function contarEmprendedores()
    {
        $count = $this->orientadorService->contarEmprendedores();
        return response()->json(['Emprendedores activos' => $count], 200);
    }

    public function mostrarOrientadores($status)
    {
        $orientadores = $this->orientadorService->mostrarOrientadores($status);
        return response()->json($orientadores, 200);
    }

    public function editarOrientador(Request $request, $id)
    {
        $data = $request->all();
        $orientador = $this->orientadorService->editarOrientador($id, $data);
        return response()->json(['message' => 'Orientador actualizado correctamente', 'data' => $orientador], 200);
    }

    public function userProfileOrientador($id)
    {
        $orientador = $this->orientadorService->obtenerPerfil($id);
        return response()->json($orientador, 200);
    }
}

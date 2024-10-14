<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmprendedorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class EmprendedorApiController extends Controller
{
    protected $emprendedorService;

    public function __construct(EmprendedorService $emprendedorService)
    {
        $this->emprendedorService = $emprendedorService;
    }

    public function show($id_emprendedor)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["message" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            $empresas = $this->emprendedorService->obtenerEmpresas($id_emprendedor);
            return response()->json($empresas, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateEmprendedor(Request $request, $documento)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["message" => "No tienes permisos para editar el perfil"], 401);
            }

            $data = $request->all();
            $this->emprendedorService->actualizarEmprendedor($documento, $data);

            return response()->json(['message' => 'Datos del emprendedor actualizados correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy($documento)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para desactivar la cuenta"], 401);
            }

            $this->emprendedorService->desactivarEmprendedor($documento);

            return response()->json(['message' => 'Emprendedor desactivado exitosamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function tipoDocumento()
    {
        return response()->json($this->emprendedorService->obtenerTiposDocumento());
    }
}

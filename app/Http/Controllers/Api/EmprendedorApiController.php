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

    // Constructor que inicializa el servicio del emprendedor
    public function __construct(EmprendedorService $emprendedorService)
    {
        $this->emprendedorService = $emprendedorService;
    }

    /**
     * Muestra las empresas asociadas a un emprendedor específico.
     */
    public function show($id_emprendedor)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["message" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            // Llama al servicio para obtener las empresas del emprendedor.
            $empresas = $this->emprendedorService->obtenerEmpresas($id_emprendedor);
            return response()->json($empresas, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

     /**
     * Actualiza los datos del perfil del emprendedor.
     */
    public function updateEmprendedor(Request $request, $documento)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["message" => "No tienes permisos para editar el perfil"], 401);
            }

            $data = $request->all();
            // Llama al servicio para actualizar el perfil del emprendedor.
            $this->emprendedorService->actualizarEmprendedor($documento, $data);

            return response()->json(['message' => 'Datos del emprendedor actualizados correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

     /**
     * Desactiva la cuenta de un emprendedor específico.
     */
    public function destroy($documento)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para desactivar la cuenta"], 401);
            }

            // Llama al servicio para desactivar la cuenta del emprendedor.
            $this->emprendedorService->desactivarEmprendedor($documento);

            return response()->json(['message' => 'Emprendedor desactivado exitosamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
    
    /**
     * Obtiene los tipos de documento disponibles para emprendedores.
     */
    public function tipoDocumento()
    {
        // Llama al servicio para obtener los tipos de documentos y los devuelve en la respuesta.
        return response()->json($this->emprendedorService->obtenerTiposDocumento());
    }
}

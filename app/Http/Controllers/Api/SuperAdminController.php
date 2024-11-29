<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\SuperAdminService;
use Exception;

class SuperAdminController extends Controller
{

    protected $superAdminService;

    public function __construct(SuperAdminService $superAdminService)
    {
        $this->superAdminService = $superAdminService;
    }


    /**
     * Actualiza la personalización del sistema para un superadmin específico.
     * Solo el rol de superadmin (id_rol = 1) tiene acceso.
     */

    public function personalizacionSis(Request $request, $id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['message' => 'No tienes permiso para acceder a esta ruta'], 401);
        }

        $data = $request->all();
        $response = $this->superAdminService->updatePersonalizacion($id, $data);

        return response()->json($response, $response['status']);
    }

    public function obtenerPersonalizacion($id)
    {
        // Obtener la respuesta del servicio
        $result = $this->superAdminService->obtenerPersonalizacion($id);

        // Verificar si hay un error
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        // Retornar la personalización formateada
        return response()->json($result['data'], $result['status']);
    }


    /**
     * Crea un nuevo superadmin, validando los datos ingresados.
     */

    public function crearSuperAdmin(Request $request)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $result = $this->superAdminService->crearSuperAdmin($request);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }
    

    /**
     * Obtiene los datos del perfil de un superadmin, junto con la información de su ubicación.
     */

    public function userProfileAdmin($id)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['message' => 'No tienes permiso para esta función']);
        }
        return $this->superAdminService->userProfileAdmin($id);
    }
    

    public function mostrarSuperAdmins(Request $request)
    {
        if (Auth::user()->id_rol != 1) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 401); // Retornar error si no tiene permiso
        }
        $estado = $request->input('estado', 'Activo');
        return $this->superAdminService->mostrarSuperAdmins($estado);
    }



    /**
     * Actualiza un recurso especificado en el almacenamiento.
     */

    public function editarSuperAdmin(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permiso para esta función'], 403);
            }

            $admin = $this->superAdminService->editarSuperAdmin($request, $id);

            return response()->json(['message' => 'Superadministrador actualizado correctamente', 'admin' => $admin], 200);
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], $statusCode);
        }
    }


    public function restore($id)
    {
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'No tienes permiso para acceder a esta ruta'], 401);
            }
            $response = $this->superAdminService->restore($id);
            return response()->json(['message' => $response['message']], $response['status']);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar la personalización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Listar aliados para actividad
    public function listarAliados()
    {
        try {
            // Verifica si el usuario autenticado tiene uno de los roles permitidos (1, 3 o 4)
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permiso para esta funcion'], 400); // Retorna un error si no tiene permisos
            }
            // Retorna la lista de aliados en formato JSON con código de estado 200 (éxito)
            return $this->superAdminService->listarAliados();
        } catch (Exception $e) {
            // Si ocurre un error, lo captura y devuelve un mensaje de error con un código de estado 401
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 401);
        }
    }
}

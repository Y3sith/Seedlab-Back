<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApoyoService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Apoyo_por_EmpresaController extends Controller
{
    protected $apoyoEmpresaService;

    // Constructor que inyecta el servicio de apoyo empresa.
    public function __construct(ApoyoService $apoyoEmpresaService)
    {
        $this->apoyoEmpresaService = $apoyoEmpresaService;
    }

    /**
     * Función para crear empresa y apoyos
     */
    public function crearApoyos(Request $request)
    {
        try {
            // Verificar el rol del usuario (rol 5 como ejemplo)
            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
            }

            // Validar los datos de la solicitud
            $validator = Validator::make($request->all(), [
                'documento' => 'required|string|max:20|unique:apoyo_empresa,documento',
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'cargo' => 'nullable|string|max:50',
                'telefono' => 'nullable|string|max:20',
                'celular' => 'nullable|string|max:20',
                'email' => 'required|email|unique:apoyo_empresa,email',
                'id_tipo_documento' => 'required|integer|exists:tipo_documento,id',
                'id_empresa' => 'required|integer|exists:empresa,documento',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Datos inválidos', 'details' => $validator->errors()], 422);
            }

            // Preparar los datos para crear el registro
            $data = $request->only([
                'documento',
                'nombre',
                'apellido',
                'cargo',
                'telefono',
                'celular',
                'email',
                'id_tipo_documento',
                'id_empresa'
            ]);

            // Llamar al servicio para crear el apoyo con empresa
            $apoyo = $this->apoyoEmpresaService->crearApoyo($data);

            return response()->json([
                'message' => 'Apoyo con empresa creado exitosamente',
                'data' => $apoyo
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para obtener los apoyos asociados a una empresa específica.
     */
    public function getApoyosxEmpresa($id_empresa)
    {
        try {
            // Verifica si el usuario tiene rol 5 (probablemente un rol específico que tiene permiso)
            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            // Obtiene todos los apoyos relacionados con la empresa especificada
            $apoyos = $this->apoyoEmpresaService->getApoyosxEmpresa($id_empresa);
            return response()->json($apoyos, 200);
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Endpoint para editar un apoyo basado en su documento.
     */
    public function editarApoyo(Request $request, $documento)
    {
        try {
            // Verifica que el usuario tenga el rol 5.
            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'No tienes permiso para acceder'], 403);
            }

            // Recopila los datos proporcionados en el request.
            $data = [
                'documento' => $request->input('documento'),
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'cargo' => $request->input('cargo'),
                'telefono' => $request->input('telefono'),
                'celular' => $request->input('celular'),
                'email' => $request->input('email'),
                'id_tipo_documento' => $request->input('id_tipo_documento'),
            ];

            // Llama al servicio para actualizar el apoyo basado en el documento.
            $apoyo = $this->apoyoEmpresaService->editarApoyo($documento, $data);

            // Si no se encuentra el apoyo, retorna un error.
            if (!$apoyo) {
                return response()->json(['error' => 'No se encontró el apoyo con el documento proporcionado'], 404);
            }

            // Retorna una respuesta exitosa indicando que el apoyo fue editado.
            return response()->json(['message' => 'Apoyo editado exitosamente'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
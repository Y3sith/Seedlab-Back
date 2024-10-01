<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApoyoEmpresa;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Log;

class Apoyo_por_EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //muestra los apoyos 
        if (Auth::user()->id_rol != 1) {
            return response()->json([
                'message' => 'No tiene permisos para acceder a este recurso'
            ], 403);
        }
        $apoyoxempresa = ApoyoEmpresa::paginate(5);
        return new JsonResponse($apoyoxempresa->items());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function crearApoyos(Request $request)
    {
        try {
            // Verifica si el usuario tiene rol 5 (probablemente un rol específico que tiene permiso)
            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            // Crea un nuevo registro de apoyo en la base de datos
            $apoyo = ApoyoEmpresa::create([
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

            // Devuelve un mensaje de éxito con un código de estado 201
            return response()->json(['message' => 'Apoyo creado con exito'], 201);
            
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error
            Log::error('Error al crear el apoyo: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(), // Log del contenido del request para depuración
                'user_id' => Auth::id(), // Para saber qué usuario realizó la acción
            ]);

            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getApoyosxEmpresa($id_empresa)
    {
        try {
            // Verifica si el usuario tiene rol 5 (probablemente un rol específico que tiene permiso)
            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            // Obtiene todos los apoyos relacionados con la empresa especificada
            $apoyos = ApoyoEmpresa::where('id_empresa', $id_empresa)->get(); // Cambiar all() por get()
            return response()->json($apoyos, 200);
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
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
    public function editarApoyo(Request $request, $documento)
    {
        try {
            // Verifica si el usuario tiene rol 5 (probablemente un rol específico que tiene permiso)
            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            // Busca el apoyo por documento
            $apoyo = ApoyoEmpresa::where('documento', $documento);

            // Verifica si se encontró el apoyo
            if (!$apoyo) {
                return response()->json(['error' => 'Apoyo no encontrado'], 404);
            }

            // Actualiza los datos del apoyo con la información del request
            $apoyo->update([
                'documento' => $request->input('documento'),
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'cargo' => $request->input('cargo'),
                'telefono' => $request->input('telefono'),
                'celular' => $request->input('celular'),
                'email' => $request->input('email'),
                'id_tipo_documento' => $request->input('id_tipo_documento'),
            ]);

            // Retorna un mensaje de éxito
            return response()->json([
                'message' => 'Apoyo editado exitosamente'
            ], 201);
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function getApoyoxDocumento($documento)
{
    try {
        // Verifica si el usuario tiene rol 5 para acceder a esta función
        if (Auth::user()->id_rol != 5) {
            return response()->json(['error' => 'no tienes permiso para acceder']);
        }

        // Busca el apoyo en la base de datos utilizando el documento
        $apoyos = ApoyoEmpresa::all()->where('documento', $documento)->first();

        // Retorna el apoyo encontrado
        return response()->json($apoyos, 200);
    } catch (Exception $e) {
        // Manejo de excepciones en caso de error durante la ejecución
        return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
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

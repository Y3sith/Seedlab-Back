<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Respuesta;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;  // Agregar esta línea


class RespuestasApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seccion = Seccion::with(['preguntas.subpreguntas.respuestas', 'preguntas.respuestas'])
            ->where('id', $id)
            ->first();

        if (!$seccion) {
            return response()->json(['message' => 'Seccion no encontrada'], 404);
        }


        return response()->json($seccion, 200);
    }

    public function guardarRespuestas(Request $request)
    {
        $idEmpresa = $request->input('id_empresa');

        // Verificar si ya existe un registro de respuestas para la primera vez
        $primeraRespuesta = Respuesta::where('id_empresa', $idEmpresa)
            ->where('verform_pr', 1)
            ->first();

        if (!$primeraRespuesta) {
            // Si no hay respuestas previas, se está llenando por primera vez
            $respuestas = new Respuesta();
            $respuestas->verform_pr = 1;  // Indicar que es la primera vez
            $respuestas->verform_se = 0;  // Aún no se llena la segunda vez
        } else {
            // Si ya existe un registro para la primera vez, se crea uno nuevo para la segunda vez
            $segundaRespuesta = Respuesta::where('id_empresa', $idEmpresa)
                ->where('verform_se', 1)
                ->first();

            if ($segundaRespuesta) {
                return response()->json(['message' => 'El formulario ya fue llenado dos veces'], 400);
            }

            // Crear un nuevo registro para la segunda vez
            $respuestas = new Respuesta();
            $respuestas->verform_pr = 0;  // No es la primera vez
            $respuestas->verform_se = 1;  // Indicar que es la segunda vez
        }

        // Guardar las nuevas respuestas
        $jsonRespuestas = json_encode($request->input('respuestas'));
        $respuestas->respuestas_json = $jsonRespuestas;
        $respuestas->id_empresa = $idEmpresa;
        $respuestas->save();

        return response()->json(['message' => 'Respuestas guardadas correctamente'], 200);
    }




    public function getAnswers($id_empresa)
    {
        $respuestas = Respuesta::where('id_empresa', $id_empresa)->first();

        if (!$respuestas) {
            return response()->json([
                'message' => 'No se encontraron respuestas para esta empresa'
            ], 404);
        }

        return response()->json([
            'respuestas' => json_decode($respuestas->respuestas_json)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

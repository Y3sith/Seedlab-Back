<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Respuesta;
use App\Models\Seccion;
use Illuminate\Http\Request;
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
        $respuestas = $request->input('respuestas');

        $jsonRespuestas = json_encode($respuestas);
        $respuestas = new Respuesta();
        $respuestas->respuestas_json = $jsonRespuestas;
        $respuestas->id_empresa = $request->input('id_empresa');
        $respuestas->save();

        /*foreach ($respuestas as $respuestaData) {
            Respuesta::create([
                'opcion' => $respuestaData['opcion'] ?? null,
                'texto_res' => $respuestaData['texto_res'] ?? null,
                'valor' => $respuestaData['valor'] ?? null,
                'id_pregunta' => $respuestaData['id_pregunta'] ?? null,
                'id_empresa' => $respuestaData['id_empresa'] ?? null,
                'id_subpregunta' => $respuestaData['id_subpregunta'] ?? null,
            ]);
        }*/

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

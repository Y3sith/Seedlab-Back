<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Respuesta;
use App\Models\Seccion;
use Illuminate\Http\Request;

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
        $seccion = Seccion::with(['preguntas.subpreguntas.respuestas', 'preguntas.respuestas' ])
        ->where('id',$id)
        ->first();

        if(!$seccion){
            return response()->json(['message' => 'Seccion no encontrada'], 404);
        }

        /*$respuestas = [];

        foreach ($seccion->preguntas as $pregunta) {
            foreach ($pregunta->respuestas as $respuesta){
                $respuestas[] = $respuesta;
            }
            foreach ($pregunta->subpreguntas as $subpregunta) {
                foreach ($subpregunta->respuestas as $respuesta){
                    $respuestas[] = $respuesta;
                }
            }
        }

        foreach($respuestas as $respuesta){
            Respuesta::create($respuesta->toArray());
        }*/

        return response()->json($seccion, 200);
    }

    public function guardarRespuestas(Request $request)
    {
        $respuestas = $request->input('respuestas');

        foreach ($respuestas as $respuestaData) {
            Respuesta::create([
                'opcion' => $respuestaData['opcion'] ?? null,
                'texto_res' => $respuestaData['texto_res'] ?? null,
                'valor' => $respuestaData['valor'] ?? null,
                'id_pregunta' => $respuestaData['id_pregunta'] ?? null,
                'id_empresa' => $respuestaData['id_empresa'] ?? null,
                'id_subpregunta' => $respuestaData['id_subpregunta'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Respuestas guardadas correctamente'], 200);
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

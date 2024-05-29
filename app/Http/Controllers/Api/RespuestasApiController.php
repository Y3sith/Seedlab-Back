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
        $data = $request->validate([
            'responses' => 'required|array',
            'responses.*.id_pregunta' => 'required|exists:pregunta,id',
            'responses.*.id_subpregunta' => 'nullable|exists:subpregunta,id',
            'responses.*.opcion' => 'nullable|string|max:10',
            'responses.*.texto_res' => 'nullable|string',
            'responses.*.valor' => 'nullable|numeric',
            'responses.*.verform_pr' => 'nullable|boolean',
            'responses.*.verform_se' => 'nullable|boolean',
            'responses.*.id_empresa' => 'required|exists:empresa,documento',
        ]);

        foreach ($data['responses'] as $response) {
            Respuesta::create([
                'id_pregunta' => $response['id_pregunta'],
                'id_subpregunta' => $response['id_subpregunta'] ?? null,
                'opcion' => $response['opcion'] ?? '',
                'texto_res' => $response['texto_res'] ?? '',
                'valor' => $response['valor'] ?? 0,
                'verform_pr' => $response['verform_pr'] ?? null,
                'verform_se' => $response['verform_se'] ?? null,
                'fecha_reg' => now(),
                'id_empresa' => $response['id_empresa']
            ]);
        }

        return response()->json(['message' => 'Respuestas guardadas exitosamente'], 201);
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

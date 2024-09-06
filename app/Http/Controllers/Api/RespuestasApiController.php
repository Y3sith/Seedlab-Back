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
        $seccion = Seccion::with(['preguntas.subpreguntas.respuestas', 'preguntas.respuestas' ])
        ->where('id',$id)
        ->first();

        if(!$seccion){
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

    public function procesarRespuestas($idEmprendedor)
    {
        // Obtener las respuestas para el emprendedor especificado
        $respuestas = DB::table('respuesta')
            ->join('empresa', 'respuesta.id_empresa', '=', 'empresa.documento')
            ->where('empresa.id_emprendedor', $idEmprendedor)
            ->select('respuesta.respuestas_json')
            ->get();

        // Obtener todos los id_pregunta y id_subpregunta únicos del JSON
        $idsPreguntas = [];
        $idsSubpreguntas = [];
        foreach ($respuestas as $respuesta) {
            $respuestas_array = json_decode($respuesta->respuestas_json, true);
            foreach ($respuestas_array as $respuesta_json) {
                if (isset($respuesta_json['id_pregunta'])) {
                    $idsPreguntas[] = $respuesta_json['id_pregunta'];
                }
                if (isset($respuesta_json['id_subpregunta'])) {
                    $idsSubpreguntas[] = $respuesta_json['id_subpregunta'];
                }
            }
        }
        $idsPreguntas = array_unique($idsPreguntas);
        $idsSubpreguntas = array_unique($idsSubpreguntas);

        // Obtener los nombres de las preguntas y subpreguntas para los ids únicos
        $preguntas = DB::table('pregunta')
            ->whereIn('id', $idsPreguntas)
            ->pluck('nombre', 'id');

        $subpreguntas = DB::table('subpregunta')
            ->whereIn('id', $idsSubpreguntas)
            ->pluck('texto', 'id');

        // Array para almacenar los resultados procesados
        $resultados = [];

        foreach ($respuestas as $respuesta) {
            $respuestas_array = json_decode($respuesta->respuestas_json, true);
            if (is_array($respuestas_array)) {
                foreach ($respuestas_array as $respuesta_json) {
                    $idPregunta = $respuesta_json['id_pregunta'] ?? null;
                    $idSubpregunta = $respuesta_json['id_subpregunta'] ?? null;
                    $resultados[] = [
                        'opcion' => $respuesta_json['opcion'] ?? null,
                        'valor' => $respuesta_json['valor'] ?? null,
                        'verform_pr' => $respuesta_json['verform_pr'] ?? null,
                        'fecha_reg' => $respuesta_json['fecha_reg'] ?? null,
                        'id_pregunta'=>$respuesta_json['id_pregunta'] ?? null,
                        'pregunta' => $preguntas[$idPregunta] ?? 'Pregunta desconocida',
                        'subpregunta' => $subpreguntas[$idSubpregunta] ?? 'Subpregunta desconocida',
                        'respuesta_texto' => $respuesta_json['texto_res'] ?? null, // Añadir el texto_res
                    ];
                }
            } else {
                Log::error('JSON inválido o no decodificable: ' . $respuesta->respuestas_json);
            }
        }

        return response()->json($resultados);
    }

}

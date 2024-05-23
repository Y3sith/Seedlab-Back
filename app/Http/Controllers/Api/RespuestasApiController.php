<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Respuesta;
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
        //
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

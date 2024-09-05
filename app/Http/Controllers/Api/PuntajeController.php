<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\puntaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuntajeController extends Controller
{
    public function store(Request $request, $documentoEmpresa)
    {
        $data = $request->validate([
            'info_general' => 'nullable|numeric',
            'info_financiera' => 'nullable|numeric',
            'info_mercado' => 'nullable|numeric',
            'info_trl' => 'nullable|numeric',
            'info_tecnica' => 'nullable|numeric',
            'documento_empresa' => 'required|integer',
            'ver_form' => 'nullable|boolean',
        ]);

        // Obtener el registro de puntaje de la empresa
        $puntaje = DB::table('puntaje')
            ->where('documento_empresa', $documentoEmpresa)
            ->first();

        $puntaje = Puntaje::updateOrCreate(
            ['documento_empresa' => $data['documento_empresa']],
            [
                'info_general' => $data['info_general'],
                'info_financiera' => $data['info_financiera'],
                'info_mercado' => $data['info_mercado'],
                'info_trl' => $data['info_trl'],
                'info_tecnica' => $data['info_tecnica'],
                'ver_form' => $puntaje->ver_form + 1,
            ]
        );

        return response()->json(['message' => 'Puntajes guardados correctamente', 'puntaje' => $puntaje]);
    }


    public function getPuntajeXEmpresa($documento_empresa)
    {

        $puntaje = Puntaje::where('documento_empresa', $documento_empresa)->first();
        if (!$puntaje) {
            return response()->json(['message' => 'No se encontraron puntajes para este emprendedor'], 404);
        }
        return response()->json($puntaje);
    }
}

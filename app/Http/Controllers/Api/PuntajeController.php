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
        // Validar los datos del request
        $data = $request->validate([
            'info_general' => 'nullable|numeric',
            'info_financiera' => 'nullable|numeric',
            'info_mercado' => 'nullable|numeric',
            'info_trl' => 'nullable|numeric',
            'info_tecnica' => 'nullable|numeric',
            'documento_empresa' => 'required|integer',
            'ver_form' => 'nullable|numeric',
        ]);

        // Verificar si ya existe un registro con ver_form_pr = 1 (primera vez)
        $primerPuntaje = Puntaje::where('documento_empresa', $documentoEmpresa)
            ->where('verform_pr', 1)
            ->first();

        if (!$primerPuntaje) {
            // No existe un puntaje para la primera vez, entonces lo creamos
            $nuevoPuntaje = new Puntaje();
            $nuevoPuntaje->documento_empresa = $data['documento_empresa'];
            $nuevoPuntaje->info_general = $data['info_general'];
            $nuevoPuntaje->info_financiera = $data['info_financiera'];
            $nuevoPuntaje->info_mercado = $data['info_mercado'];
            $nuevoPuntaje->info_trl = $data['info_trl'];
            $nuevoPuntaje->info_tecnica = $data['info_tecnica'];
            $nuevoPuntaje->verform_pr = 1;  // Indica que es la primera vez
            $nuevoPuntaje->verform_se = 0;  // Aún no se llena la segunda vez
            $nuevoPuntaje->save();

            return response()->json(['message' => 'Puntaje guardado correctamente (primera vez)', 'puntaje' => $nuevoPuntaje]);
        } else {
            // Ya existe un puntaje para la primera vez, revisamos si existe para la segunda vez
            $segundoPuntaje = Puntaje::where('documento_empresa', $documentoEmpresa)
                ->where('verform_se', 1)
                ->first();

            if ($segundoPuntaje) {
                // Si ya existe el puntaje de la segunda vez, no permitimos otro registro
                return response()->json(['message' => 'El formulario ya fue llenado dos veces'], 400);
            }

            // Crear un nuevo puntaje para la segunda vez
            $nuevoPuntaje = new Puntaje();
            $nuevoPuntaje->documento_empresa = $data['documento_empresa'];
            $nuevoPuntaje->info_general = $data['info_general'];
            $nuevoPuntaje->info_financiera = $data['info_financiera'];
            $nuevoPuntaje->info_mercado = $data['info_mercado'];
            $nuevoPuntaje->info_trl = $data['info_trl'];
            $nuevoPuntaje->info_tecnica = $data['info_tecnica'];
            $nuevoPuntaje->verform_pr = 0;  // No es la primera vez
            $nuevoPuntaje->verform_se = 1;  // Indica que es la segunda vez
            $nuevoPuntaje->save();

            return response()->json(['message' => 'Puntaje guardado correctamente (segunda vez)', 'puntaje' => $nuevoPuntaje]);
        }
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

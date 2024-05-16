<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsesoriaxAsesor;
use App\Models\Aliado;
use App\Models\Emprendedor;
use App\Models\Asesoria;
use App\Models\HorarioAsesoria;


class AsesoriasController extends Controller
{

    public function Guardarasesoria(Request $request)
    {
        $aliado = Aliado::where('nombre', $request->input('nom_aliado'))->first();
        $emprendedor = Emprendedor::find($request->input('doc_emprendedor'))->first();
        if (!$emprendedor) {
            return response(['message' => 'Emprendedor no encontrado',], 404);
        }
        if (!$aliado) {
            return response()->json(['error' => 'No se encontró ningún aliado con el nombre proporcionado.'], 404);
        }

        $asesoria = Asesoria::create([
            'Nombre_sol' => $request->input('nombre'),
            'notas' => $request->input('notas'),
            'isorientador' => $request->input('isorientador'),
            'asignacion' => $request->input('asignacion'),
            'fecha' => $request->input('fecha'),
            'id_aliado' => $aliado->id,
            'doc_emprendedor' => $request->input('doc_emprendedor'),
        ]);

        return response()->json(['message' => 'La asesoria se ha solicitado con exito'], 201);
    }

    public function asignarasesoria(Request $request){
        $newasesoria = Asesoriaxasesor::create([
            'id_asesoria' => $request->input('id_asesoria'),
            'id_asesor' => $request->input('id_asesor'),
        ]);

        return response()->json(['insercion' => $newasesoria], 201);
    }


    public function definirhorarioasesoria(Request $request){

            $horarioAsesoria = HorarioAsesoria::create([
                'observacion' => $request -> input('observacion'),
                'fecha' => $request -> input('fecha'),
                'estado' => $request -> input('estado'),
                'id_asesoria' => $request -> input('id_asesoria'),
            ]);
            return response()->json($horarioAsesoria);
    }

    public function editarasignacionasesoria(){

    }



}

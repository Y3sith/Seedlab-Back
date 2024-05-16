<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;



class AliadoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aliados = Aliado::whereHas('auth', function ($query) {
            $query->where('estado', 1);
        })->select('nombre', 'descripcion', 'logo', 'ruta_multi', 'id_tipo_dato')->get();
        return response()->json($aliados);
    }

    public function crearaliado(Request $data)
    {
        $response = null;
        $statusCode = 200;

        DB::transaction(function () use ($data, &$response, &$statusCode) {
            $results = DB::select('CALL sp_registrar_aliado(?, ?, ?, ?, ?, ?, ?, ?)', [
                $data['nombre'],
                $data['logo'],
                $data['descripcion'],
                $data['tipodato'],
                $data['ruta'],
                $data['email'],
                Hash::make($data['password']),
                $data['estado'],
            ]); 

            if (!empty($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El nombre del aliado ya se encuentra registrado' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                    $statusCode = 400;
                } 
            }
        });

        return response()->json(['message' => $response], $statusCode);

        
    }

    public function mostrarAliado(Request $request)
    {

        $aliado = Aliado::find($request->input('id'));

        if ($aliado) {
            // Codificar el logo en base64 si está presente
            $logoBase64 = $aliado->logo ? 'data:image/png;base64,' . $aliado->logo : null;

            return response()->json([
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $logoBase64,
                'ruta_multi' => $aliado->ruta_multi,
                'id_autentication' => $aliado->id_autentication,
                'id_tipo_dato' => $aliado->id_tipo_dato,
                'estado' => $aliado->estado,
            ]);
        } else {
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

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
    public function destroy($id)
    {
        $aliado = Aliado::find($id);

        if (!$aliado) {
            return response()->json([
                'message' => 'Aliado no encontrado',
            ], 404);
        }

        $aliado->update([
            'estado' => 0,
        ]);

        return response()->json([
            'message' => 'Aliado desactivado',
        ], 200); // Cambiado el código de estado a 200, que indica éxito en lugar de 404
    }

    public function MostrarAsesorAliado($id)
    {
        $aliado = Aliado::find($id);  

        if(!$aliado) {
        return response()->json(['message' => 'No se encontró ningún aliado este ID'], 404);
        }

        $asesores = Aliado::findorFail($id)->asesor()->select('nombre', 'apellido', 'celular')->get();
        return response()->json($asesores);
    }

}

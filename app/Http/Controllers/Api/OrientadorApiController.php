<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Asesoria;
use App\Models\Aliado;


class OrientadorApiController extends Controller
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
    public function createOrientador(Request $data)
    {
        $response = null;
        $statusCode = 200;

        DB::transaction(function()use ($data, &$response, &$statusCode){
             $results = DB::select('CALL sp_registrar_orientador(?,?,?,?,?,?)', [
                  $data['nombre'],
                  $data['apellido'],
                  $data['celular'],
                  $data['email'],
                  Hash::make($data['password']),
                  $data['estado'],
            ]);

            if (!empty($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El correo electrónico ya ha sido registrado anteriormente' || $response === 'El numero de celular ya ha sido registrado en el sistema') {
                    $statusCode = 400;
                }
            }
        });
        return response()->json(['message' => $response], $statusCode);

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

    public function asignarAliado(Request $request, $idAsesoria) {
        $nombreAliado = $request->input('nombreAliado');

        $asesoria = Asesoria::find($idAsesoria);
        if (!$asesoria) {
            return response()->json(['message' => 'Asesoría no encontrada'], 404);
        }

        $aliado = Aliado::where('nombre', $nombreAliado)->first();
        if (!$aliado) {
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }

        $asesoria->id_aliado = $aliado->id;
        $asesoria->save();

        return response()->json(['message' => 'Aliado asignado correctamente'], 200);
    }
    /*
    EJ de Json para "asignarAliado"
    {
	"nombreAliado": "Ecopetrol"
    } 
    */
}

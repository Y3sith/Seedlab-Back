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
    public function Traeraliadosactivos()
    {
        $aliados = Aliado::whereHas('auth', fn($query) => $query->where('estado', 1))
            ->with('tipoDato:id,nombre')
            ->select('nombre', 'descripcion', 'logo', 'ruta_multi', 'id_tipo_dato')
            ->get();

        $aliadosTransformados = $aliados->map(fn($aliado) => [
            'nombre' => $aliado->nombre,
            'descripcion' => $aliado->descripcion,
            'logo' => $aliado->logo,
            'ruta_multi' => $aliado->ruta_multi,
            'tipo_dato' => $aliado->tipoDato->nombre,
        ]);

        return response()->json($aliadosTransformados);
    }

    public function crearaliado(Request $data)
    {
        $response = null;
        $statusCode = 200;

        if(strlen($data['password']) <8) {
            $statusCode = 400;
            $response = 'La contraseña debe tener al menos 8 caracteres';
            return response()->json(['message' => $response], $statusCode);
        }

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
        $aliado = Aliado::with(['auth', 'tipoDato'])->find($request->input('id'));

        if ($aliado) {
            $logoBase64 = $aliado->logo ? 'data:image/png;base64,' . $aliado->logo : null;

            $estado = $aliado->auth ? $aliado->auth->estado : null;

            $tipoDato = $aliado->tipoDato ? $aliado->tipoDato->nombre : null;

            return response()->json([
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $logoBase64,
                'ruta_multi' => $aliado->ruta_multi,
                'id_autentication' => $aliado->id_autentication,
                'id_tipo_dato' => $tipoDato,
                'estado' => $estado == 1 ? "Activo" : "Inactivo",
            ]);
        } else {
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }
    }

    public function Editaraliado(Request $request)
    {
        $aliado = Aliado::find($request->input('id'));

        if ($aliado) {
            $aliado->nombre = $request->input('nombre');
            $aliado->descripcion = $request->input('descripcion');
            $aliado->logo = $request->input('logo');
            $aliado->ruta_multi = $request->input('ruta_multi');
            $aliado->save();
    
            if ($aliado->auth) {
                $user = $aliado->auth;
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password')); 
                $user->estado = $request->input('estado'); 
                $user->save();
            }
            return response()->json(['message' => 'Aliado actualizado correctamente']);
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
        if(Auth::user()->id_rol != 3){
            return response()->json([
               'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
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

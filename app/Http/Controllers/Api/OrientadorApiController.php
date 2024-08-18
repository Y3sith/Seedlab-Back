<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use App\Models\Asesoria;
use App\Models\Orientador;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        try {
            $response = null;
            $statusCode = 200;

            if (strlen($data['password']) < 8) {
                $statusCode = 400;
                $response = 'La contraseña debe tener al menos 8 caracteres';
                return response()->json(['message' => $response], $statusCode);
            }
            if (Auth::user()->id_rol !== 1) {
                return response()->json(["error" => "No tienes permisos para crear un orientador"], 401);
            }

            $perfilUrl = null;
                if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                    $logoPath = $data->file('imagen_perfil')->store('public/fotoPerfil');
                    $perfilUrl = Storage::url($logoPath);
                }
                //dd($data->all());
                
            DB::transaction(function () use ($data, &$response, &$statusCode,$perfilUrl) {
                Log::info($data->all());
                $results = DB::select('CALL sp_registrar_orientador(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['apellido'],
                    $perfilUrl,
                    $data['direccion'],
                    $data['celular'],
                    $data['genero'],
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
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
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

    public function asignarAsesoriaAliado(Request $request, $idAsesoria)
    {
        try {
            if (Auth::user()->id_rol != 2) {
                return response()->json([
                    'message' => 'No tienes permiso para acceder a esta ruta',
                ], 401);
            }
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
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    /*
    EJ de Json para "asignarAliado"
    {
    "nombreAliado": "Ecopetrol"
    }
     */

    public function listarAliados()
    {
        if (Auth::user()->id_rol != 2 && Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        $usuarios = User::where('estado', true)
            ->where('id_rol', 3)
            ->pluck('id');

        $aliados = Aliado::whereIn('id_autentication', $usuarios)
            ->get(['nombre']);

        return response()->json($aliados, 200);
    }

    public function contarEmprendedores()
    {
        $enumerar = User::where('id_rol', 5)->where('estado', true)->count();

        return response()->json(['Emprendedores activos' => $enumerar]);
    }

    public function mostrarOrientadores($status)
    {

        if (Auth::user()->id_rol !== 1 && Auth::user()->id_rol !== 2) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        $orientadores = Orientador::select('orientador.id', 'orientador.nombre', 'orientador.apellido', 'orientador.celular', 'orientador.id_autentication')
            ->join('users', 'orientador.id_autentication', '=', 'users.id')
            ->where('users.estado', $status)
            ->get();

        $orientadoresConEstado = $orientadores->map(function ($orientador) {
            $user = User::find($orientador->id_autentication);

            return [
                'id' => $orientador->id,
                'nombre' => $orientador->nombre,
                'apellido' => $orientador->apellido,
                'celular' => $orientador->celular,
                'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo',
                'email' => $user->email,
                'id_auth' => $orientador->id_autentication,
            ];
        });

        return response()->json($orientadoresConEstado);
    }





    public function editarOrientador(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 2 && Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }
            $orientador = Orientador::find($id);
            if ($orientador) {
                $orientador->nombre = $request->input('nombre');
                $orientador->apellido = $request->input('apellido');
               // $orientador->celular = $request->input('celular');
               $newCelular = $request->input('celular');
               $orientador->direccion = $request->input('direccion');
               $orientador->genero = $request->input('genero');
                    if ($newCelular && $newCelular !== $orientador->celular) {
                        // Verificar si el nuevo email ya está en uso
                        $existing = Orientador::where('celular', $newCelular)->first();
                        if ($existing) {
                            return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 402);
                        }
                        $orientador->celular = $newCelular;
                    }

                    if ($request->hasFile('imagen_perfil')) {
                        //Eliminar el logo anterior
                        Storage::delete(str_replace('storage', 'public', $orientador->imagen_perfil));
                        
                        // Guardar el nuevo logo
                        $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
                        $orientador->imagen_perfil = str_replace('public', 'storage', $path);
                    } 

                $orientador->save();

                if ($orientador->auth) {
                    $user = $orientador->auth;
                    $password = $request->input('password');
                    if ($password) {
                        $user->password =  Hash::make($request->input('password'));
                    }

                    $newEmail = $request->input('email');
                    if ($newEmail && $newEmail !== $user->email) {
                        // Verificar si el nuevo correo electrónico ya existe
                        $existingUser = User::where('email', $newEmail)->first();
                        if ($existingUser) {
                            return response()->json(['message' => 'El correo electrónico ya ha sido registrado anteriormente'], 400);
                        }
                        $user->email = $newEmail;
                    }
                    $user->estado = $request->input('estado');
                    $user->save();
                }
                return response()->json(['message' => 'Orientador actualizado correctamente'], 200);
            } else {
                return response()->json(['message' => 'Orientador no encontrado'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    private function correctImageUrl($path)
    {
        // Elimina cualquier '/storage' inicial
        $path = ltrim($path, '/storage');

        // Asegúrate de que solo haya un '/storage' al principio
        return url('storage/' . $path);
    }

    public function userProfileOrientador($id)
    {
        try {
            if (Auth::user()->id_rol != 2 && Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }
            $orientador = Orientador::where('id', $id)
                ->with('auth:id,email,estado')
                //->select('id', 'nombre', 'apellido', 'celular', "id_autentication")
                ->first();
            $response = [
                'id' => $orientador->id,
                'nombre' => $orientador->nombre,
                'apellido' => $orientador->apellido,
                'imagen_perfil'=>$orientador->imagen_perfil ? $this->correctImageUrl($orientador->fotoPerfil) : null,
                'direccion' => $orientador->direccion,
                'celular' => $orientador->celular,
                'genero' => $orientador->genero,
                'id_auth' => $orientador->auth->id,
                'email' => $orientador->auth->email,
                'estado' => $orientador->auth->estado == 1 ? 'Activo' : 'Inactivo'

            ];
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}

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
     * Crea un nuevo orientador en el sistema.
     */
    public function createOrientador(Request $data)
    {
        try {
            $response = null;
            $statusCode = 200;

            // Validar que la contraseña tenga al menos 8 caracteres.
            if (strlen($data['password']) < 8) {
                $statusCode = 400;
                $response = 'La contraseña debe tener al menos 8 caracteres';
                return response()->json(['message' => $response], $statusCode);
            }

            // Verificar que el usuario tenga permisos para crear un orientador.
            if (Auth::user()->id_rol != 1) {
                return response()->json(["error" => "No tienes permisos para crear un orientador"], 401);
            }

            // Obtener dirección y fecha de nacimiento, con valores por defecto si no se proporcionan.
            $direccion = $data->input('direccion', 'Dirección por defecto');
            $fecha_nac = $data->input('fecha_nac', '2000-01-01');

            // Manejo de la imagen de perfil.
            $imagen_perfil = null;
            if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                $imagenPath = $data->file('imagen_perfil')->store('fotoPerfil', 'public');
                $imagen_perfil = Storage::url($imagenPath);
            }

            // Realizar la operación de creación dentro de una transacción.
            DB::transaction(function () use ($data, &$response, &$statusCode, $direccion, $fecha_nac, $imagen_perfil) {
                // Llamada al procedimiento almacenado para registrar el orientador.
                $results = DB::select('CALL sp_registrar_orientador(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['apellido'],
                    $data['documento'],
                    $imagen_perfil,
                    $data['celular'],
                    $data['genero'],
                    $direccion,
                    $data['id_tipo_documento'],
                    $data['departamento'],
                    $data['municipio'],
                    $fecha_nac,
                    $data['email'],
                    Hash::make($data['password']),
                    $data['estado'],

                ]);

                // Procesar el resultado de la creación.
                if (!empty($results)) {
                    $response = $results[0]->mensaje;
                    // Verificar si hubo errores de duplicidad.
                    if ($response === 'El correo electrónico ya ha sido registrado anteriormente' || $response === 'El numero de celular ya ha sido registrado en el sistema') {
                        $statusCode = 400;
                    }
                }
            });

            // Devolver la respuesta final.
            return response()->json(['message' => $response], $statusCode);
        } catch (Exception $e) {
            // Manejar errores inesperados.
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    //Asigna un aliado a una asesoría específica.
    public function asignarAsesoriaAliado(Request $request, $idAsesoria)
    {
        try {
            // Verificar que el usuario tenga el rol adecuado.
            if (Auth::user()->id_rol != 2) {
                return response()->json([
                    'message' => 'No tienes permiso para acceder a esta ruta',
                ], 401);
            }

            // Obtener el nombre del aliado desde la solicitud.
            $nombreAliado = $request->input('nombreAliado');

            // Buscar la asesoría por su ID.
            $asesoria = Asesoria::find($idAsesoria);
            if (!$asesoria) {
                return response()->json(['message' => 'Asesoría no encontrada'], 404);
            }

            // Buscar el aliado por su nombre.
            $aliado = Aliado::where('nombre', $nombreAliado)->first();
            if (!$aliado) {
                return response()->json(['message' => 'Aliado no encontrado'], 404);
            }

            // Asignar el aliado a la asesoría.
            $asesoria->id_aliado = $aliado->id;
            $asesoria->save();

            // Devolver un mensaje de éxito.
            return response()->json(['message' => 'Aliado asignado correctamente'], 200);
        } catch (Exception $e) {
            // Manejar errores inesperados y devolver un mensaje de error.
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    //Lista los aliados activos.
    public function listarAliados()
    {
        // Verificar si el usuario tiene el rol adecuado (2 o 5).
        if (Auth::user()->id_rol != 2 && Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        // Obtener los IDs de los usuarios que están activos y tienen el rol de aliado (rol 3).
        $usuarios = User::where('estado', true)
            ->where('id_rol', 3)
            ->pluck('id');

        // Buscar los aliados que coinciden con los IDs de usuarios obtenidos.
        $aliados = Aliado::whereIn('id_autentication', $usuarios)
            ->get(['nombre']);

        // Devolver la lista de aliados en formato JSON.
        return response()->json($aliados, 200);
    }

    //Cuenta la cantidad de emprendedores activos.
    public function contarEmprendedores()
    {
        // Contar los usuarios que tienen el rol de emprendedor (rol 5) y están activos.
        $enumerar = User::where('id_rol', 5)->where('estado', true)->count();

        // Devolver la cuenta en formato JSON.
        return response()->json(['Emprendedores activos' => $enumerar]);
    }

    //Muestra una lista de orientadores con su estado.
    public function mostrarOrientadores($status)
    {
        // Verificar permisos del usuario autenticado.
        if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 2) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
        }

        // Obtener los orientadores cuyos usuarios tienen el estado especificado.
        $orientadores = Orientador::select('orientador.id', 'orientador.nombre', 'orientador.apellido', 'orientador.celular', 'orientador.id_autentication')
            ->join('users', 'orientador.id_autentication', '=', 'users.id')
            ->where('users.estado', $status)
            ->get();

        // Mapear los resultados para incluir información adicional.
        $orientadoresConEstado = $orientadores->map(function ($orientador) {
            $user = User::find($orientador->id_autentication);

            return [
                'id' => $orientador->id,
                'nombre' => $orientador->nombre,
                'apellido' => $orientador->apellido,
                'celular' => $orientador->celular,
                'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo', // Definir el estado como "Activo" o "Inactivo"
                'email' => $user->email,
                'id_auth' => $orientador->id_autentication,
            ];
        });

        // Devolver la lista de orientadores en formato JSON.
        return response()->json($orientadoresConEstado, 200);
    }




    //Actualiza la información de un orientador.
    public function editarOrientador(Request $request, $id)
    {
        try {
            // Verificar si el usuario autenticado tiene permisos para editar orientadores.
            if (Auth::user()->id_rol != 2 && Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            // Buscar el orientador por ID.
            $orientador = Orientador::find($id);
            if ($orientador) {
                // Actualizar los campos del orientador.
                $orientador->nombre = $request->input('nombre');
                $orientador->apellido = $request->input('apellido');
                $orientador->documento = $request->input('documento');
                $newCelular = $request->input('celular');
                $orientador->direccion = $request->input('direccion');
                $orientador->genero = $request->input('genero');
                $orientador->id_tipo_documento = $request->input('id_tipo_documento');
                $orientador->id_departamento = $request->input('id_departamento');
                $orientador->id_municipio = $request->input('id_municipio');
                $orientador->fecha_nac = $request->input('fecha_nac');

                // Verificar si el celular ya está registrado y actualizar el celular.
                if ($newCelular && $newCelular !== $orientador->celular) {
                    $existing = Orientador::where('celular', $newCelular)->first();
                    if ($existing) {
                        return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 402);
                    }
                    $orientador->celular = $newCelular;
                }

                if ($request->hasFile('imagen_perfil')) {
                    // Eliminar la imagen anterior si existe.
                    Storage::delete(str_replace('storage', 'public', $orientador->imagen_perfil));

                    // Guardar la nueva imagen de perfil.
                    $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
                    $orientador->imagen_perfil = str_replace('public', 'storage', $path);
                }

                // Guardar los cambios del orientador.
                $orientador->save();

                // Si el orientador tiene un usuario asociado, actualizar la información del usuario.
                if ($orientador->auth) {
                    $user = $orientador->auth;
                    $password = $request->input('password');
                    if ($password) {
                        $user->password = Hash::make($password);
                    }

                    $newEmail = $request->input('email');
                    if ($newEmail && $newEmail !== $user->email) {
                        // Verificar si el nuevo correo electrónico ya existe.
                        $existingUser = User::where('email', $newEmail)->first();
                        if ($existingUser) {
                            return response()->json(['message' => 'El correo electrónico ya ha sido registrado anteriormente'], 400);
                        }
                        $user->email = $newEmail;
                    }

                    // Actualizar el estado del usuario.
                    $user->estado = $request->input('estado');
                    $user->save();
                }
                return response()->json(['message' => 'Orientador actualizado correctamente', $orientador], 200);
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

    //Obtiene el perfil de un orientador por su ID.
    public function userProfileOrientador($id)
    {
        try {
            // Verifica si el usuario autenticado tiene permiso para acceder a esta función.
            if (Auth::user()->id_rol != 2 && Auth::user()->id_rol != 1) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }

            // Obtiene los datos del orientador junto con la información del municipio y departamento.
            $orientador = Orientador::where('orientador.id', $id)
                ->join('municipios', 'orientador.id_municipio', '=', 'municipios.id')
                ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id')
                ->select(
                    'orientador.id',
                    'orientador.nombre',
                    'orientador.apellido',
                    'orientador.documento',
                    'orientador.id_tipo_documento',
                    'orientador.imagen_perfil',
                    'orientador.direccion',
                    'orientador.celular',
                    'orientador.fecha_nac',
                    'orientador.genero',
                    'orientador.id_municipio',
                    'municipios.nombre as municipio_nombre',
                    'departamentos.name as departamento_nombre',
                    'departamentos.id as id_departamento',
                    'orientador.id_autentication'
                )
                ->first();
            // Devuelve los datos del orientador en un formato estructurado.
            return [
                'id' => $orientador->id,
                'nombre' => $orientador->nombre,
                'apellido' => $orientador->apellido,
                'documento' => $orientador->documento,
                'id_tipo_documento' => $orientador->id_tipo_documento,
                'fecha_nac' => $orientador->fecha_nac,
                'imagen_perfil' => $orientador->imagen_perfil ? $this->correctImageUrl($orientador->imagen_perfil) : null,
                'direccion' => $orientador->direccion,
                'celular' => $orientador->celular,
                'genero' => $orientador->genero,
                'id_municipio' => $orientador->id_municipio,
                'id_departamento' => $orientador->id_departamento,
                'email' => $orientador->auth->email,
                'estado' => $orientador->auth->estado == 1 ? 'Activo' : 'Inactivo',
            ];
        } catch (Exception $e) {
            // Captura cualquier excepción y devuelve un mensaje de error.
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}

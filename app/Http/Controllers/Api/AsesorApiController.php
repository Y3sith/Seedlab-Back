<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionCrearUsuario;
use Illuminate\Support\Facades\Auth;
use App\Models\Asesor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\HorarioAsesoria;
use App\Models\TipoDocumento;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class AsesorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * crear
     */
    public function store(Request $data)
    {
        try {
            $response = null;
            $statusCode = 200;

            // Verifica si el usuario tiene permisos (rol 3)
            if (Auth::user()->id_rol != 3) {
                $statusCode = 400;
                $response = 'Solo los aliados pueden crear asesores';
                return response()->json(['message' => $response], $statusCode);
            }

            $generateRandomPassword = function($length = 8) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $password = '';
                for ($i = 0; $i < $length; $i++) {
                    $password .= $characters[rand(0, strlen($characters) - 1)];
                }
                return $password;
            };
            
            $randomPassword = $generateRandomPassword();
            $hashedPassword = Hash::make($randomPassword);

            // Define valores por defecto
            $direccion = $data->input('direccion', 'Dirección por defecto');
            $fecha_nac = $data->input('fecha_nac', '2000-01-01');

            // Manejo de la imagen de perfil
            $imagen_perfil = null;
            if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                $imagenPath = $data->file('imagen_perfil')->store('fotoPerfil', 'public');
                $imagen_perfil = Storage::url($imagenPath);
            }

            // Transacción de base de datos
            DB::transaction(function () use ($data, &$response, &$statusCode, $direccion, $fecha_nac, $imagen_perfil, $hashedPassword, $randomPassword) {
                $results = DB::select('CALL sp_registrar_asesor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['apellido'],
                    $data['documento'],
                    $imagen_perfil,
                    $data['celular'],
                    $data['genero'],
                    $direccion,
                    $data['aliado'], // no el id, el nombre
                    $data['id_tipo_documento'],
                    $data['departamento'],
                    $data['municipio'],
                    $fecha_nac,
                    $data['email'],
                    $hashedPassword,
                    $data['estado'],
                ]);
                // Verifica si hay resultados y maneja errores
                if (!empty($results)) {
                    $response = $results[0]->mensaje;
                    if ($response === 'El numero de celular ya ha sido registrado en el sistema' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                        $statusCode = 400;
                    }else{
                        $email = $results[0]->email; 
                        $rol = 'Asesor';
                        if ($email) {
                            \Log::info("Intentando enviar correo a: " . $email);
                            Mail::to($email)->send(new NotificacionCrearUsuario($email, $rol, $randomPassword));
                        } else {
                            \Log::warning("No se pudo enviar el correo porque $email está vacío");
                        }
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
    public function updateAsesor(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }

            $requiredFields = [
                'nombre',
                'apellido',
                'documento',
                'celular',
                'genero',
                'direccion',
                'id_tipo_documento',
                'id_departamento',
                'id_municipio',
                'fecha_nac',
                'celular'
            ];

            // Verifica que todos los campos requeridos estén presentes
            foreach ($requiredFields as $field) {
                if (empty($request->input($field))) {
                    return response()->json(['message' => "Debes completar todos los campos requeridos."], 400);
                }
            }

            // Busca el asesor por ID
            $asesor = Asesor::find($id);
            if ($asesor) {
                $asesor->nombre = $request->input('nombre');
                $asesor->apellido = $request->input('apellido');
                $newCelular = $request->input('celular');
                $asesor->documento = $request->input('documento');
                $asesor->direccion = $request->input('direccion');
                $asesor->genero = $request->input('genero');
                $asesor->fecha_nac = $request->input('fecha_nac');
                $asesor->id_tipo_documento = $request->input('id_tipo_documento');
                $asesor->id_departamento = $request->input('id_departamento');
                $asesor->id_municipio = $request->input('id_municipio');

                $newCelular = $request->input('celular');
                if ($newCelular && $newCelular !== $asesor->celular) {
                    // Verificar si el nuevo email ya está en uso
                    $existing = Asesor::where('celular', $newCelular)->first();
                    if ($existing) {
                        return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 400);
                    }
                    $asesor->celular = $newCelular;
                }

                if ($request->hasFile('imagen_perfil')) {
                    // Eliminar el logo anterior
                    Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));

                    // Guardar el nuevo logo
                    $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
                    $asesor->imagen_perfil = str_replace('public', 'storage', $path);
                }

                $asesor->save();

                if ($asesor->auth) {
                    $user = $asesor->auth;
                    $password = $request->input('password');
                    if ($password) {
                        if (strlen($password) < 8) {
                            return response()->json(['message' => 'La contraseña debe tener al menos 8 caracteres'], 400);
                        }
                        $user->password =  Hash::make($request->input('password'));
                    }
                }
                $user->save();

            return response()->json(['message' => 'Asesor actualizado correctamente', $asesor], 200);
            }
            return response()->json(['message' => 'Asesor no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function updateAsesorxaliado(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'no tienes permiso para esta funcion']);
            }

            $asesor = Asesor::find($id);
            if ($asesor) {
                $asesor->nombre = $request->input('nombre');
                $asesor->apellido = $request->input('apellido');
                $newCelular = $request->input('celular');
                $asesor->documento = $request->input('documento');
                $asesor->direccion = $request->input('direccion');
                $asesor->genero = $request->input('genero');
                $asesor->fecha_nac = $request->input('fecha_nac');
                $asesor->id_tipo_documento = $request->input('id_tipo_documento');
                $asesor->id_departamento = $request->input('id_departamento');
                $asesor->id_municipio = $request->input('id_municipio');

                $newCelular = $request->input('celular');
                if ($newCelular && $newCelular !== $asesor->celular) {
                    // Verificar si el nuevo celular ya está en uso
                    $existing = Asesor::where('celular', $newCelular)->first();
                    if ($existing) {
                        return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 400);
                    }
                    $asesor->celular = $newCelular;
                }

                if ($request->hasFile('imagen_perfil')) {
                    // Eliminar la imagen anterior
                    Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));

                    // Guardar la nueva imagen
                    $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
                    $asesor->imagen_perfil = str_replace('public', 'storage', $path);
                }


                $asesor->save();

                if ($asesor->auth) {
                    $user = $asesor->auth;
                    $password = $request->input('password');
                    if ($password) {
                        if (strlen($password) < 8) {
                            return response()->json(['message' => 'La contraseña debe tener al menos 8 caracteres'], 400);
                        }
                        $user->password =  Hash::make($request->input('password'));
                    }

                    $newEmail = $request->input('email');
                    if ($newEmail && $newEmail !== $user->email) {
                        // Verificar si el nuevo email ya está en uso
                        $existingUser = User::where('email', $newEmail)->first();
                        if ($existingUser) {
                            return response()->json(['message' => 'El correo electrónico ya ha sido registrado anteriormente'], 400);
                        }
                        $user->email = $newEmail;
                    }

                    $user->estado = $request->input('estado');
                    $user->save();
                }
                return response()->json(['message' => 'Asesor actualizado correctamente'], 200);
            }
            return response()->json(['message' => 'Asesor no encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Verifica si el usuario autenticado tiene el rol adecuado (rol 3)
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        // Busca el asesor por el ID proporcionado
        $asesor = Asesor::find($id);
        if (!$asesor) {
            return response()->json([
                'message' => 'Asesor no encontrado',
            ], 404);
        }

        // Accede al usuario asociado al asesor
        $user = $asesor->auth;
        $user->estado = 0;
        $user->save();

        // Devuelve un mensaje de éxito
        return response()->json([
            'message' => 'Asesor desactivado',
        ], 200);
    }

    public function mostrarAsesoriasAsesor($id, $conHorario)
    {
        // Busca el asesor por el ID proporcionado
        $asesor = Asesor::find($id);

        // Verifica si el asesor existe
        if (!$asesor) {
            return response()->json([
                'message' => 'El asesor no existe en el sistema'
            ], 404);
        }

        // Obtiene todas las asesorías asociadas al asesor, incluyendo emprendedor y horarios
        $asesoriasAsesor = $asesor->asesorias()->with('emprendedor', 'horarios')->get();

        // Filtra las asesorías según el parámetro conHorario
        if ($conHorario === 'true') {
            $asesoriasFiltradas = $asesoriasAsesor->filter(function ($asesoria) {
                return $asesoria->horarios->isNotEmpty();
            });
        } else {
            $asesoriasFiltradas = $asesoriasAsesor->filter(function ($asesoria) {
                return $asesoria->horarios->isEmpty();
            });
        }

        // Mapea las asesorías filtradas a un formato específico
        $resultado = $asesoriasFiltradas->map(function ($asesoria) {
            $data = [
                'id' => $asesoria->id,
                'Nombre_sol' => $asesoria->Nombre_sol,
                'notas' => $asesoria->notas,
                'fecha' => $asesoria->fecha,
                'nombre' => $asesoria->emprendedor->nombre,
                'apellido' => $asesoria->emprendedor->apellido,
                'celular' => $asesoria->emprendedor->celular,
                'correo' => $asesoria->emprendedor->auth->email,
            ];

            // Si hay horarios, agrega sus detalles al resultado
            if ($asesoria->horarios->isNotEmpty()) {
                $data['observaciones'] = $asesoria->horarios->first()->observaciones;
                $data['fecha_asignacion'] = $asesoria->horarios->first()->fecha;
                $data['estado'] = $asesoria->horarios->first()->estado;
            } else {
                $data['mensaje'] = 'No tiene horario asignado';
            }
            return $data;
        })->values();

        // Retorna el resultado en formato JSON
        return response()->json($resultado, 200);
    }

    public function contarAsesorias($idAsesor)
    {
        // Busca el asesor por el ID proporcionado
        $asesor = Asesor::find($idAsesor);

        // Verifica si el asesor existe
        if (!$asesor) {
            return response()->json([
                'error' => 'Asesor no encontrado'
            ], 404);
        }

        // Cuenta las asesorías finalizadas asociadas al asesor
        $finalizadas = $asesor->asesorias()->whereHas('horarios', function ($query) {
            $query->where('estado', 'Finalizada');
        })->count();

        // Cuenta las asesorías pendientes asociadas al asesor
        $pendientes = $asesor->asesorias()->whereHas('horarios', function ($query) {
            $query->where('estado', 'Pendiente');
        })->count();

        // Retorna el conteo de asesorías en formato JSON
        return response()->json([
            'Asesorias finalizadas' => $finalizadas,
            'Asesorias Pendientes' => $pendientes,
        ]);
    }

    private function correctImageUrl($path)
    {
        // Elimina cualquier '/storage' inicial
        $path = ltrim($path, '/storage');

        // Asegúrate de que solo haya un '/storage' al principio
        return url('storage/' . $path);
    }

    public function userProfileAsesor($id)
    {
        try {
            // Verifica si el usuario tiene permisos para acceder a esta función
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol != 3) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            // Realiza una consulta para obtener el perfil del asesor por su ID
            $asesor = Asesor::where('asesor.id', $id)
                ->join('municipios', 'asesor.id_municipio', '=', 'municipios.id') // Une con la tabla de municipios
                ->join('departamentos', 'municipios.id_departamento', '=', 'departamentos.id') // Une con la tabla de departamentos
                ->select(
                    'asesor.id',
                    'asesor.nombre',
                    'asesor.apellido',
                    'asesor.documento',
                    'asesor.id_tipo_documento',
                    'asesor.imagen_perfil',
                    'asesor.direccion',
                    'asesor.celular',
                    'asesor.fecha_nac',
                    'asesor.genero',
                    'asesor.id_municipio',
                    'departamentos.id as id_departamento',
                    'asesor.id_autentication'
                )
                ->first();

            // Devuelve un arreglo con la información del perfil del asesor
            return [
                'id' => $asesor->id,
                'nombre' => $asesor->nombre,
                'apellido' => $asesor->apellido,
                'documento' => $asesor->documento,
                'id_tipo_documento' => $asesor->id_tipo_documento,
                'fecha_nac' => $asesor->fecha_nac,
                'imagen_perfil' => $asesor->imagen_perfil ? $this->correctImageUrl($asesor->imagen_perfil) : null,
                'direccion' => $asesor->direccion,
                'celular' => $asesor->celular,
                'genero' => $asesor->genero,
                'id_departamento' => $asesor->id_departamento,
                'id_municipio' => $asesor->id_municipio,
                'email' => $asesor->auth->email, // Obtiene el email del asesor
                'estado' => $asesor->auth->estado == 1 ? 'Activo' : 'Inactivo',
            ];
        } catch (Exception $e) {
            // Manejo de excepciones y retorno de error en caso de fallo
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    public function listarAsesores()
    {
        try {
            // Verifica si el usuario tiene rol de administrador (id_rol == 1)
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            // Obtiene todos los asesores, seleccionando solo los campos 'id' y 'nombre'
            $asesores = Asesor::all()->select('id', 'nombre');

            // Devuelve la lista de asesores en formato JSON
            return response()->json($asesores);
        } catch (Exception $e) {
            // Manejo de excepciones en caso de error
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}
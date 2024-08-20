<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Asesor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\HorarioAsesoria;
use Exception;
use Illuminate\Support\Facades\Storage;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $data)
    {
        try {
            $response = null;
            $statusCode = 200;
            if (Auth::user()->id_rol != 3) {
                $statusCode = 400;
                $response = 'Solo los aliados pueden crear asesores';
                return response()->json(['message' => $response], $statusCode);
            }
            if (strlen($data['password']) < 8) {
                $statusCode = 400;
                $response = 'La contraseña debe tener al menos 8 caracteres';
                return response()->json(['message' => $response], $statusCode);
            }

            $perfilUrl = null;
                if ($data->hasFile('imagen_perfil') && $data->file('imagen_perfil')->isValid()) {
                    $logoPath = $data->file('imagen_perfil')->store('public/fotoPerfil');
                    $perfilUrl = Storage::url($logoPath);
                }

            DB::transaction(function () use ($data, &$response, &$statusCode, $perfilUrl) {
                $results = DB::select('CALL sp_registrar_asesor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['apellido'],
                    $data['documento']=>"N/A",
                    $perfilUrl,
                    $data['direccion'],
                    $data['genero']=>"N/A",
                    $data['celular'],
                    $data['aliado'], //no el id el nombre
                    $data['email'],
                    $data['id_municipio'],
                    $data['fecha_nac']=>"N/A",
                    $data['id_tipo_documento']=>"1",
                    Hash::make($data['password']),
                    $data['estado'],
                ]);


                if (!empty($results)) {
                    $response = $results[0]->mensaje;
                    if ($response === 'El numero de celular ya ha sido registrado en el sistema' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
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
    public function update(Request $request, $id)
    {
        try {
            $asesor = Asesor::find($id);
            //dd($request->estado);
            if (Auth::user()->id_rol == 4 ) { //rol asesor

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
                        //Eliminar el logo anterior
                        Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));
                        
                        // Guardar el nuevo logo
                        $path = $request->file('imagen_perfil')->store('public/fotoPerfil');
                        $asesor->imagen_perfil = str_replace('public', 'storage', $path);
                    }    

                $asesor->update([
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'celular' => $request->celular,
                    //'imagen_perfil'=>$request->imagen_perfil,
                    'direccion'=>$request->direccion,
                    'genero'=>$request->genero,
                    //'email' => $request->email, no se sabe si pueda editar 
                ]);
                
                return response()->json(['message' => 'Asesor actualizado', 200]);
            }
            if(Auth::user()->id_rol == 3){ //rol aliado
                $user = $asesor->auth;

                $newCelular = $request->input('celular');
                if ($newCelular && $newCelular !== $asesor->celular) {
                    // Verificar si el nuevo email ya está en uso
                    $existing = Asesor::where('celular', $newCelular)->first();
                    if ($existing) {
                        return response()->json(['message' => 'El numero de celular ya ha sido registrado anteriormente'], 400);
                    }
                    $asesor->celular = $newCelular;
                }
                $asesor->update([
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'celular' => $request->celular
                ]);

                
                $password = $request->input('password');
                    if ($password) {
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
                return response()->json(['message' => 'Asesor actualizado', 200]);
                }
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
        $asesor = Asesor::find($id);
        if (!$asesor) {
            return response()->json([
                'message' => 'Asesor no encontrado',
            ], 404);
        }
        $user = $asesor->auth;
        $user->estado = 0;
        $user->save();
        return response()->json([
            'message' => 'Asesor desactivado',
        ], 200);
    }

    public function mostrarAsesoriasAsesor($id, $conHorario)
    {
        $asesor = Asesor::find($id);

        if (!$asesor) {
            return response()->json([
                'message' => 'El asesor no existe en el sistema'
            ], 404);
        }

        $asesoriasAsesor = $asesor->asesorias()->with('emprendedor', 'horarios')->get();

        if ($conHorario === 'true') {
            $asesoriasFiltradas = $asesoriasAsesor->filter(function ($asesoria) {
                return $asesoria->horarios->isNotEmpty();
            });
        } else {
            $asesoriasFiltradas = $asesoriasAsesor->filter(function ($asesoria) {
                return $asesoria->horarios->isEmpty();
            });
        }

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
            if ($asesoria->horarios->isNotEmpty()) {
                $data['observaciones'] = $asesoria->horarios->first()->observaciones;
                $data['fecha_asignacion'] = $asesoria->horarios->first()->fecha;
                $data['estado'] = $asesoria->horarios->first()->estado;
            } else {
                $data['mensaje'] = 'No tiene horario asignado';
            }
            return $data;
        })->values();

        return response()->json($resultado, 200);
    }

    public function contarAsesorias($idAsesor)
    {

        $asesor = Asesor::find($idAsesor);

        if (!$asesor) {
            return response()->json([
                'error' => 'Asesor no encontrado'
            ], 404);
        }

        $finalizadas = $asesor->asesorias()->whereHas('horarios', function ($query) {
            $query->where('estado', 'Finalizada');
        })->count();

        $pendientes = $asesor->asesorias()->whereHas('horarios', function ($query) {
            $query->where('estado', 'Pendiente');
        })->count();

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
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 4 && Auth::user()->id_rol!= 3) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }
            $asesor = Asesor::where('id', $id)
                //->with('auth:id,email,estado')
                ->select('id','nombre', 'apellido', 'imagen_perfil' ,'direccion','celular', 'genero',"id_autentication")
                ->first();
                return [
                    'id'=>$asesor->id,
                    'nombre'=>$asesor->nombre,
                    'apellido'=>$asesor->apellido,
                    'imagen_perfil'=>$asesor->imagen_perfil ? $this->correctImageUrl($asesor->imagen_perfil) : null,
                    'direccion'=>$asesor->direccion,
                    'celular'=>$asesor->celular,
                    'genero'=>$asesor->genero,
                    'email'=>$asesor->auth->email,
                    'estado'=>$asesor->auth->estado == 1 ? 'Activo': 'Inactivo',
                    //'id_autentication' =>$asesor->auth->id_autentication    
                ];
            //return response()->json($asesor);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function listarAsesores (){
        try {
            if (Auth::user()->id_rol != 1) {
                return response()->json(['error'=>'no tienes permiso para acceder']);
            }
            $asesores = Asesor::all()->select('id','nombre');
            return response()->json($asesores);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }

    }

}

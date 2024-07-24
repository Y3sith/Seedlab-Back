<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Asesoria;
use App\Models\Asesor;
use App\Models\Banner;
use App\Models\Emprendedor;
use App\Models\HorarioAsesoria;
use App\Models\TipoDato;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class AliadoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function traerAliadosActivos($status)
    {
       // 

        $aliados = Aliado::whereHas('auth', fn ($query) => $query->where('estado', $status))
            ->with(['tipoDato:id,nombre', 'auth'])
            ->select('nombre', 'descripcion', 'logo', 'ruta_multi', 'id_tipo_dato', 'id_autentication')
            ->get();

        $aliadosTransformados = $aliados->map(function ($aliado) {
            //$banner = Banner::find($aliado->$id_aliado);
            return [
                'nombre' => $aliado->nombre,
                'descripcion' => $aliado->descripcion,
                'logo' => $aliado->logo,
                //'banner' => $aliado->banner ? $this->correctImageUrl($aliado->banner) : null,
                'ruta_multi' => $aliado->ruta_multi,
                'tipo_dato' => $aliado->tipoDato,
                'email' => $aliado->auth->email,
                'estado' => $aliado->auth->estado
            ];
        });

        return response()->json($aliadosTransformados);
    }

    private function correctImageUrl($path)
    {
        // Elimina cualquier '/storage' inicial
        $path = ltrim($path, '/storage');

        // Asegúrate de que solo haya un '/storage' al principio
        return url('storage/' . $path);
    }

    public function crearAliado(Request $data)
    {
        try {
            $response = null;
            $statusCode = 200;
            $aliadoId = null;

            if (Auth::user()->id_rol != 1) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }

            if (strlen($data['password']) < 8) {
                $statusCode = 400;
                $response = 'La contraseña debe tener al menos 8 caracteres';
                return response()->json(['message' => $response], $statusCode);
            }

            if (!$data->hasFile('banner.urlImagen') || !$data->file('banner.urlImagen')->isValid()) {
                return response()->json(['error' => 'Se requiere una imagen válida para el banner'], 400);
            }
    
            DB::beginTransaction();

            try {
            DB::transaction(function () use ($data, &$response, &$statusCode, &$aliadoId) {
                $results = DB::select('CALL sp_registrar_aliado(?, ?, ?, ?, ?, ?, ?, ?)', [
                    $data['nombre'],
                    $data['logo'],
                    //$bannerUrl,
                    $data['descripcion'],
                    $data['tipodato'],
                    $data['ruta'],
                    $data['email'],
                    Hash::make($data['password']),
                    $data['estado'],
                ]);
                

                if (!empty($results)) {
                    $response = $results[0]->mensaje;
                    $aliadoId = $results[0]->id;
                    
                    if ($response === 'El nombre del aliado ya se encuentra registrado' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                        $statusCode = 400;
                        throw new \Exception($response);
                    }
                }
            });

            if (isset($aliadoId)) {
                if ($data->hasFile('banner.urlImagen') && $data->file('banner.urlImagen')->isValid()) {
                    $bannerPath = $data->file('banner.urlImagen')->store('public/banners');
                    $bannerUrl = Storage::url($bannerPath);
                
                    Banner::create([
                        'urlImagen' => $bannerUrl,
                        'descripcion' => $data['banner']['descripcion'],
                        'estadobanner' => $data['banner']['estadobanner'],
                        'color' => $data['banner']['color'],
                        'id_aliado' => $aliadoId,
                    ]);

                    DB::commit();
                    Log::info('Aliado y banner creados:', ['aliadoId' => $aliadoId, 'response' => $response]);

                    return response()->json(['message' => $response], $statusCode);
                }
            }

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error al crear aliado y banner:', ['error' => $e->getMessage()]);
                return response()->json(['error' => $e->getMessage()], 400);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    // public function crearBanner (Request $request)
    // {
    //     if (Auth::user()->id_rol != 1 && Auth::user()->id_rol !=3) {
    //         return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
    //     }

    //         $bannerUrl = null;

    //         if ($request->hasFile('urlImagen') && $request->file('urlImagen')->isValid()) {
    //             $bannerPath = $request->file('urlImagen')->store('public/banners');
    //             $bannerUrl = Storage::url($bannerPath);
    //         }

    //     $banner = Banner::create([
    //         'urlImagen' => $bannerUrl,
    //         'descripcion' => $request->descripcion,
    //         'estado' => $request->estado,
    //         'color' => $request->color,
    //         'id_aliado' => $request->id_aliado,
    //     ]);
    //     return response()->json([
    //        'message' => 'Banner creado exitosamente',
    //     ], 201);
    // }


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
                'message' => 'Aliado creado exitosamente', 200
            ]);
        } else {
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }
    }

    public function editarAliado(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 1 || Auth::user()->id_rol != 3) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }
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
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
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
        if (Auth::user()->id_rol == 3 || Auth::user()->id_rol == 1) {

            $aliado = Aliado::find($id);
            if (!$aliado) {
                return response()->json([
                    'message' => 'Aliado no encontrado',
                ], 404);
            }
            $user = $aliado->auth;
            $user->estado = 0;
            $user->save();

            return response()->json([
                'message' => 'Aliado desactivado',
            ], 200);
        }

        return response()->json([
            'message' => 'No tienes permisos para realizar esta acción'
        ], 403);
    }

    public function mostrarAsesorAliado(Request $request, $id)
    {
        try {
            if (Auth::user()->id_rol != 1 && Auth::user()->id_rol != 3) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 401);
            }
            $estado = $request->input('estado', 'Activo');
            $estadoBool = $estado === 'Activo' ? 1 : 0;
            $aliado = Aliado::find($id);
            if (!$aliado) {
                return response()->json(['message' => 'No se encontró ningún aliado con este ID'], 404);
            }
            $asesores = Aliado::findOrFail($id)->asesor()
                ->whereHas('auth', function ($query) use ($estadoBool) {
                    $query->where('estado', $estadoBool);
                })
                ->select('id', 'id_aliado','nombre', 'apellido', 'celular', 'id_autentication')
                ->get();
            $asesoresConEstado = $asesores->map(function ($asesor) {
                $user = User::find($asesor->id_autentication);
                return [
                    'id' => $asesor->id,
                    'nombre' => $asesor->nombre,
                    'apellido' => $asesor->apellido,
                    'celular' => $asesor->celular,
                    'id_aliado' => $asesor->id_aliado,
                    'estado' => $user->estado == 1 ? 'Activo' : 'Inactivo'
                ];
            });
            return response()->json($asesoresConEstado);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function dashboardAliado($idAliado)
    {
        //CONTAR ASESORIASxALIADO SEGUN SU ESTADO (PENDIENTES O FINALIZADAS)
        $finalizadas = Asesoria::where('id_aliado', $idAliado)->whereHas('horarios', function ($query) {
            $query->where('estado', 'Finalizada');
        })->count();

        $pendientes = Asesoria::where('id_aliado', $idAliado)->whereHas('horarios', function ($query) {
            $query->where('estado', 'Pendiente');
        })->count();

        $asignadas = Asesoria::where('id_aliado', $idAliado)
            ->where('asignacion', 1)
            ->count();
            

        $sinAsignar = Asesoria::where('id_aliado', $idAliado)
            ->where('asignacion', 0)
            ->count();


        //CONTAR # DE ASESORES DE ESE ALIADO
        $numAsesores = Asesor::where('id_aliado', $idAliado)->count();

        $totalAsesorias = $finalizadas + $pendientes;

        // Calcular los porcentajes
        // Calcular los porcentajes
        $porcentajeFinalizadas = $totalAsesorias > 0 ? round(($finalizadas / $totalAsesorias) * 100, 2) . '%' : 0;
        $porcentajePendientes = $totalAsesorias > 0 ? round(($pendientes / $totalAsesorias) * 100, 2) . '%' : 0;

        return response()->json([
            'Asesorias Pendientes' => $pendientes,
            'Porcentaje Pendientes' => $porcentajePendientes,
            'Asesorias Finalizadas' => $finalizadas,
            'Porcentaje Finalizadas' => $porcentajeFinalizadas,
            'Asesorias Asignadas' => $asignadas,
            'Asesorias Sin Asignar' => $sinAsignar,
            'Mis Asesores' => $numAsesores,
        ]);
    }

    public function generos() //contador de cuantos usuarios son mujer/hombres u otros
    {
        try {
            
            if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 1) {
                return response()->json(['message', 'No tienes permiso para acceder a esta funcion'], 400);
            }
            $generos = DB::table('emprendedor')
                ->select('genero', DB::raw('count(*) as total'))
                ->whereIn('genero', ['Masculino', 'Femenino','Otro'])
                ->groupBy('genero')
                ->get();

            $masculino = 0;
            $femenino = 0;
            $otro = 0;

            foreach ($generos as $genero) {
                if ($genero->genero == 'Femenino') {
                    $femenino = $genero->total;
                } else {
                    $masculino = $genero->total;
                    $otro = $genero->total;
                }
            }

            return response()->json([
                ['genero' => 'Femenino', 'total' => $femenino],
                ['genero' => 'Masculino', 'total' => $masculino],
                ['genero' => 'Otro', 'total' => $otro],
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function gestionarAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
            }

            $asesoriaId = $request->input('id_asesoria');
            $accion = $request->input('accion'); // aceptar o rechazar

            $asesoria = Asesoria::find($asesoriaId);

            if (!$asesoria || $asesoria->id_aliado != Auth::user()->aliado->id) {
                return response()->json(['message' => 'Asesoría no encontrada o no asignada a este aliado'], 404);
            }

            /*$horario = HorarioAsesoria::where('id_asesoria', $asesoriaId)->first();
    if (!$horario) {
        return response()->json(['message' => 'No se encontró un horario para esta asesoría'], 404);
    }*/

            /*if ($accion === 'aceptar') {
        $horario->estado = 'aceptada';
        $mensaje = 'Asesoría aceptada correctamente';
    } 
    */ elseif ($accion === 'rechazar') {
                //$horario->estado = 'rechazada';
                $asesoria->id_aliado = null;  // Establecer id_aliado a null
                $asesoria->isorientador = true;
                $asesoria->save(); // Guardar cambios en la asesoria
                $mensaje = 'Asesoría rechazada correctamente';
            } else {
                return response()->json(['message' => 'Acción no válida'], 400);
            }

            //$horario->save();

            return response()->json(['message' => $mensaje], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    //Se hizo un solo update en  Asesor
    // public function editarAsesorXaliado(Request $request, $id)
    // {
    //     try {
    //         if (Auth::user()->id_rol != 3) {
    //             return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
    //         }

    //         $asesor = Asesor::find($id);

    //         if ($asesor) {
    //             $asesor->nombre = $request->input('nombre');
    //             $asesor->apellido = $request->input('apellido');
    //             $asesor->celular = $request->input('celular');
    //             $asesor->save();

    //             if ($asesor->auth) {
    //                 $user = $asesor->auth;
    //                 $password = $request->input('password');
    //                 if ($password) {
    //                     $user->password =  Hash::make($request->input('password'));
    //                 }
    //                 $user->email = $request->input('email');
    //                 $user->estado = $request->input('estado');
    //                 $user->save();
    //             }
    //             return response()->json(['message' => 'Asesor actualizado correctamente']);
    //         } else {
    //             return response()->json(['message' => 'Asesor no encontrado'], 404);
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
    //     }
    // }

    public function verEmprendedoresxEmpresa()
    {
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a esta ruta'
            ], 401);
        }

        $emprendedoresConEmpresas = Emprendedor::with('empresas')->get();

        return response()->json($emprendedoresConEmpresas);
    }
}

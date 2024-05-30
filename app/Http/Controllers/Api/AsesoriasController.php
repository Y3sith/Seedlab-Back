<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aliado;
use App\Models\Asesor;
use App\Models\Asesoria;
use App\Models\AsesoriaxAsesor;
use App\Models\Emprendedor;
use App\Models\HorarioAsesoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsesoriasController extends Controller
{

    public function guardarAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }
            $emprendedor = Emprendedor::find($request->input('doc_emprendedor'));
            if (!$emprendedor) {
                return response()->json(['message' => 'Emprendedor no encontrado'], 404);
            }

            // Verificación del aliado, si se proporciona
            $aliado = null;
            if ($request->filled('nom_aliado')) {
                $aliado = Aliado::where('nombre', $request->input('nom_aliado'))->first();
                if (!$aliado) {
                    return response()->json(['error' => 'No se encontró ningún aliado con el nombre proporcionado.'], 404);
                }
            }
            $asesoria = Asesoria::create([
                'Nombre_sol' => $request->input('nombre'),
                'notas' => $request->input('notas'),
                'isorientador' => $request->input('isorientador'),
                'asignacion' => $request->input('asignacion'),
                'fecha' => $request->input('fecha'),
                'id_aliado' => $aliado ? $aliado->id : null,
                'doc_emprendedor' => $request->input('doc_emprendedor'),
            ]);
            return response()->json(['message' => 'La asesoría se ha solicitado con éxito'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    public function asignarAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción'
                ], 403);
            }
            $asesoriaexiste = Asesoriaxasesor::where('id_asesoria', $request->input('id_asesoria'))->first();

            $asesorexiste = Asesor::where('id', $request->input('id_asesor'))->first();

        if (!$asesorexiste) {
            return response()->json(['message' => 'Este asesor no existe en el sistema'], 404);
        }
        if ($asesoriaexiste) {
            return response()->json(['message' => 'Esta asesoria ya se ha asignado, edita la asignación'], 201);
        }
        $newasesoria = Asesoriaxasesor::create([
            'id_asesoria' => $request->input('id_asesoria'),
            'id_asesor' => $request->input('id_asesor'),
        ]);

            return response()->json(['message' => 'se ha asignado el asesor para esta asesoria'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }









    public function definirHorarioAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción'
                ], 403);
            }

            $idAsesoria = $request->input('id_asesoria');
            $fecha = $request->input('fecha');

            if (!$request->filled(['observaciones', 'fecha', 'estado', 'id_asesoria'])) {
                return response()->json(['message' => 'Faltan campos en la solicitud'], 400);
            }


            $asesoria = Asesoria::find($idAsesoria);
            if (!$asesoria) {
                return response()->json(['message' => 'La asesoría no existe'], 404);
            }

            $existingHorario = HorarioAsesoria::where('id_asesoria', $idAsesoria)->first();
            if ($existingHorario) {
                return response()->json(['message' => 'La asesoría ya tiene una fecha asignada'], 400);
            }

            $horarioAsesoria = HorarioAsesoria::create($request->all());
            /*$horarioAsesoria = HorarioAsesoria::create([
                'observacion' => $request->input('observacion'),
                'fecha' => $request->input('fecha'),
                'estado' => $request->input('estado'),
                'id_asesoria' => $request->input('id_asesoria'),
            ]);*/
            return response()->json(['mesage' => 'Se le a asignado un horario a su Asesoria'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function editarAsignacionAsesoria(Request $request)
    {
        if (Auth::user()->id_rol != 3 || Auth::user()->id_rol != 4) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
        $asignacion = Asesoriaxasesor::where('id_asesoria', $request->input('id_asesoria'))->first();
        if (!$asignacion) {
            return response()->json(['message' => 'La asignación no existe en el sistema'], 404);
        }

        $asesor = Asesor::find($request->input('id_asesor'));
        if (!$asesor) {
            return response()->json(['message' => 'El asesor no existe en el sistema'], 404);
        }

        $asignacion->id_asesor = $request->input('id_asesor');
        $asignacion->save();

        return response()->json(['message' => 'Se ha actualizado el asesor para esta asignación'], 200);
    }

    public function traerAsesoriasPorEmprendedor(Request $request)
    {
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $documento = $request->input('documento');
        $asignacion = $request->input('asignacion');

        $query = DB::table('asesoria as o')
            ->leftJoin('asesoriaxasesor as a', 'o.id', '=', 'a.id_asesoria')
            ->leftJoin('asesor as e', 'a.id_asesor', '=', 'e.id')
            ->leftJoin('aliado as ali', 'ali.id', '=', 'o.id_aliado')
            ->leftJoin('emprendedor as em', 'o.doc_emprendedor', '=', 'em.documento')
            ->leftJoin('horarioasesoria as hr', 'o.id', '=', 'hr.id_asesoria')
            ->where('em.documento', '=', $documento)
            ->where('o.asignacion', '=', $asignacion)
            ->orderBy('o.fecha', 'desc');

        if ($asignacion) {
            $query->select(
                'o.id as id_asesoria',
                'o.Nombre_sol',
                'o.notas',
                'o.fecha as fecha_solicitud',
                'ali.nombre',
                'a.id_asesor',
                DB::raw('CONCAT(e.nombre, " ", e.apellido) as Asesor'),
                'hr.fecha',
                'hr.estado',
                'hr.observaciones as observaciones_asesor'
            );
        } else {
            $query->select(
                'o.id as id_asesoria',
                'o.Nombre_sol',
                'o.notas',
                'o.fecha as fecha_solicitud',
                DB::raw('IFNULL(ali.nombre, "Orientador - En espera de redireccionamiento") as nombre')
            );
        }

        $asesorias = $query->get();

        return response()->json($asesorias);
    }

    public function traerasesoriasorientador(Request $request)
    {
        if (Auth::user()->id_rol != 2) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
        $Asignado = $request->input('pendiente');

        $asesorias = Asesoria::with(['emprendedor.auth'])
            ->where('isorientador', true)
            ->when($Asignado, function ($query) {
                $query->whereNull('id_aliado');
            }, function ($query) {
                $query->whereNotNull('id_aliado');
            })
            ->get()
            ->map(function ($asesoria) {
                $data = [
                    'id' => $asesoria->id,
                    'Nombre_sol' => $asesoria->Nombre_sol,
                    'notas' => $asesoria->notas,
                    'fecha' => $asesoria->fecha,
                    'documento' => $asesoria->emprendedor->documento,
                    'nombres' => $asesoria->emprendedor->nombres,
                    'celular' => $asesoria->emprendedor->celular,
                    'email' => $asesoria->emprendedor->auth->email
                ];
                if ($asesoria->aliado && $asesoria->aliado->nombre) {
                    $data['aliado_redirigido'] = $asesoria->aliado->nombre;
                }
                return $data;
            });
        return response()->json($asesorias);
    }

    public function asignarAliado(Request $request, $idAsesoria)
    {
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

    public function MostrarAsesorias($aliadoId, $asignacion)
    {
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }
        $aliado = Aliado::find($aliadoId);

        if (!$aliado) {
            return response()->json(['message' => 'No se encontró ningún aliado con este ID'], 404);
        }

        $asesorias = Asesoria::with(['emprendedor', 'asesoriaxAsesor.asesor', 'horarios'])
            ->where('id_aliado', $aliado->id)
            ->where('asignacion', $asignacion)
            ->get()
            ->map(function ($asesoria) {

                $asesor = $asesoria->asesoriaxAsesor->first() ? $asesoria->asesoriaxAsesor->first()->asesor : null;
                $horario = $asesoria->horarios->first();

                $data = [
                    'id_asesoria' => $asesoria->id,
                    'Nombre_sol' => $asesoria->Nombre_sol,
                    'notas' => $asesoria->notas,
                    'fecha_solicitud' => $asesoria->fecha,
                    'Emprendedor' => $asesoria->emprendedor ? $asesoria->emprendedor->nombre . ' ' . $asesoria->emprendedor->apellido : null,
                ];

                if ($horario && $horario->fecha) {
                    $data['Asesor'] = $asesor ? $asesor->nombre . ' ' . $asesor->apellido : null;
                    $data['fecha_horario'] = $horario->fecha;
                    $data['estado'] = $horario->estado;
                    $data['observaciones_asesor'] = $horario->observaciones;
                } else if ($asesor) {
                    $data['Asesor'] = $asesor ? $asesor->nombre . ' ' . $asesor->apellido : null;
                    $data['mensaje'] = 'El asesor aún no ha asignado horario';
                }

                return $data;
            });

        return response()->json($asesorias);
    }



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function traerAsesoriasParaAliado(Request $request)
    {
        if (Auth::user()->id_rol != 3) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        $idAliado = Auth::user()->aliado->id;  // Asumiendo que el usuario autenticado tiene una relación con el modelo Aliado
        $estado = $request->input('estado', 'pendiente'); // Estado de la asesoría: pendiente, aceptada, rechazada

        $asesorias = Asesoria::with(['emprendedor', 'horarios'])
            ->where('id_aliado', $idAliado)
            ->whereHas('horarios', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->get();
        return response()->json($asesorias);
    }

    public function listarasesoresdisponibles($idaliado)
    {
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'], 403);
        }
        $asesores = Asesor::selectRaw(
            'asesor.id as id_asesor,
            CONCAT(asesor.nombre, " ",asesor.apellido) as nombre_completo,
            MAX(horarioasesoria.fecha) as ultima_fecha_asesoria,
            CONCAT(
                TIMESTAMPDIFF(DAY, MAX(horarioasesoria.fecha), NOW()), " días con ", 
                TIMESTAMPDIFF(HOUR, MAX(horarioasesoria.fecha), NOW()) % 24, " horas"
            ) as tiempo_desde_ultima_asesoria'
        )
        ->leftJoin('users', 'asesor.id_autentication', '=', 'users.id')
        ->leftJoin('asesoriaxasesor', 'asesoriaxasesor.id_asesor', '=', 'asesor.id')
        ->leftJoin('horarioasesoria', 'asesoriaxasesor.id_asesoria', '=', 'horarioasesoria.id_asesoria')
        ->where('asesor.id_aliado', $idaliado)
        ->whereRaw('users.estado = true')
        ->groupBy('asesor.id','nombre_completo')
        ->get();
    
        return $asesores;
    
    }

    // http://127.0.0.1:8000/api/asesorias/asesores_disponibles/2
    // respuesta de ejemplo
    // [
    //     {
    //         "id_asesor": 1,
    //         "nombres": " ",
    //         "ultima_fecha_asesoria": "2024-04-23 14:30:00",
    //         "tiempo_desde_ultima_asesoria": "34 días con 20 horas"
    //     }
    // ]

}

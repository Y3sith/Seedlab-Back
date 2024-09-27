<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionAsesoriaAsesor;
use App\Mail\NotificacionAsesoriaEmprendedor;
use App\Mail\NotificacionesAsesoriaAliado;
use App\Models\Aliado;
use App\Models\Asesor;
use App\Models\Asesoria;
use App\Models\AsesoriaxAsesor;
use App\Models\Emprendedor;
use App\Models\HorarioAsesoria;
use App\Models\Orientador;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionAsesoria;

class AsesoriasController extends Controller
{

    public function guardarAsesoria(Request $request)
    {
        try {
            // Verifica si el usuario autenticado tiene el rol de emprendedor (id_rol = 5)
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            // Busca al emprendedor en la base de datos usando el documento proporcionado
            $emprendedor = Emprendedor::find($request->input('doc_emprendedor'));
            if (!$emprendedor) {
                return response()->json(['message' => 'Emprendedor no encontrado'], 404);
            }

            // Determina si se trata de un orientador
            $isOrientador = $request->input('isorientador') == 1;
            $destinatario = null;

            // Si es orientador, busca el siguiente orientador activo
            if ($isOrientador) {
                $destinatario = $this->siguientOrientador();
                if (!$destinatario) {
                    return response()->json(['error' => 'No hay orientadores activos disponibles.'], 400);
                }
            } else {
                // Lógica para aliado
                if ($request->filled('nom_aliado')) {
                    $aliado = Aliado::where('nombre', $request->input('nom_aliado'))->first();
                    if (!$aliado) {
                        return response()->json(['error' => 'No se encontró ningún aliado con el nombre proporcionado.'], 404);
                    }
                    $destinatario = $aliado; // Asigna el aliado encontrado como destinatario
                }
            }

            // Verifica que se haya asignado un destinatario (aliado u orientador)
            if (!$destinatario) {
                return response()->json(['message' => 'Necesitas asignar ya sea un aliado u orientador'], 400);
            }

            // Crea una nueva asesoría en la base de datos
            $asesoria = Asesoria::create([
                'Nombre_sol' => $request->input('nombre'),
                'notas' => $request->input('notas'),
                'isorientador' => $isOrientador,
                'asignacion' => $request->input('asignacion'),
                'fecha' => $request->input('fecha'),
                'id_aliado' => $isOrientador ? null : $destinatario->id,
                'id_orientador' => $isOrientador ? $destinatario->id : null,
                'doc_emprendedor' => $request->input('doc_emprendedor'),
            ]);

            // Carga la relación de autenticación del destinatario (aliado u orientador)
            $destinatario->load('auth');
            // Envía un correo al destinatario con la notificación de la asesoría
            if ($destinatario->auth && $destinatario->auth->email) {
                Mail::to($destinatario->auth->email)->send(new NotificacionAsesoria($asesoria, $destinatario, $emprendedor, $isOrientador));
            } else {
                $tipo = $isOrientador ? 'orientador' : 'aliado';
            }

            // Devuelve una respuesta indicando que la asesoría se ha solicitado con éxito
            return response()->json(['message' => 'La asesoría se ha solicitado con éxito'], 201);
        } catch (Exception $e) {
            // Manejo de errores: devuelve un mensaje de error y el código de estado 500
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    private function siguientOrientador()
    {
        // Obtener todos los orientadores activos
        $orientadoresActivos = Orientador::whereHas('auth', function ($query) {
            $query->where('estado', 1);
        })->get();

        if ($orientadoresActivos->isEmpty()) {
            return null;
        }

        // Obtener la última asesoría asignada a un orientador
        $ultimaAsesoria = Asesoria::where('isorientador', true)
            ->orderBy('id', 'desc')
            ->first();

        if (!$ultimaAsesoria) {
            // Si no hay asesorías previas, devolver el primer orientador
            return $orientadoresActivos->first();
        }

        // Encontrar el índice del último orientador asignado
        $ultimoIndex = $orientadoresActivos->search(function ($orientador) use ($ultimaAsesoria) {
            return $orientador->id == $ultimaAsesoria->id_orientador;
        });

        // Si no se encuentra (puede pasar si el orientador ya no está activo), comenzar desde el principio
        if ($ultimoIndex === false) {
            return $orientadoresActivos->first();
        }

        // Calcular el índice del próximo orientador
        $proximoIndex = ($ultimoIndex + 1) % $orientadoresActivos->count();

        return $orientadoresActivos[$proximoIndex];
    }


    public function asignarAsesoria(Request $request)
    {
        try {
            // Verifica si el usuario autenticado tiene el rol de asesor (id_rol = 3)
            if (Auth::user()->id_rol != 3) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción'
                ], 403);
            }

            $destinatario = null;

            // Busca si ya existe una asignación para la asesoría
            $asesoriaexiste = Asesoriaxasesor::where('id_asesoria', $request->input('id_asesoria'))->first();

            // Busca al asesor en la base de datos usando el ID proporcionado
            $asesorexiste = Asesor::where('id', $request->input('id_asesor'))->first();
            if (!$asesorexiste) {
                return response()->json(['error' => 'No se encontró ningún asesor con el nombre proporcionado.'], 404);
            }
            $destinatario = $asesorexiste; // Asigna el asesor encontrado como destinatario

            // Verifica si la asesoría ya está asignada
            if ($asesoriaexiste) {
                return response()->json(['message' => 'Esta asesoria ya se ha asignado, edita la asignación'], 201);
            }

            // Crea una nueva relación entre la asesoría y el asesor
            $newasesoria = Asesoriaxasesor::create([
                'id_asesoria' => $request->input('id_asesoria'),
                'id_asesor' => $request->input('id_asesor'),
            ]);

            // Actualiza el estado de la asesoría para indicar que ha sido asignada
            $asesoria = Asesoria::find($request->input('id_asesoria'));
            $asesoria->asignacion = 1; // Cambia este valor según el estado deseado
            $asesoria->save();

            // Obtiene el nombre del asesor asignado
            $asesor = Asesor::find($request->input('id_asesor'));
            $nombreAsesor = $asesor ? $asesor->nombre : 'Asesor desconocido';

            // Busca al emprendedor relacionado con la asesoría
            $emprendedor = Emprendedor::find($asesoria->doc_emprendedor);
            $nombreEmprendedor = $emprendedor ? $emprendedor->nombre : 'Emprendedor desconocido';

            // Carga la relación de autenticación del asesor
            $destinatario->load('auth');
            // Envía un correo al asesor con la notificación de asignación
            if ($destinatario->auth && $destinatario->auth->email) {
                Mail::to($destinatario->auth->email)->send(new NotificacionAsesoriaAsesor($destinatario, $asesoria,  $nombreAsesor, $nombreEmprendedor));
            }

            // Devuelve una respuesta indicando que la asignación se ha realizado correctamente
            return response()->json(['message' => 'Se ha asignado correctamente el asesor para esta asesoria'], 201);
        } catch (Exception $e) {
            // Manejo de errores: devuelve un mensaje de error y el código de estado 500
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function definirHorarioAsesoria(Request $request)
    {
        try {
            // Verifica si el usuario autenticado tiene el rol de asesor (id_rol = 4)
            if (Auth::user()->id_rol != 4) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción'
                ], 403);
            }
            $destinatario = null;
            $idAsesoria = $request->input('id_asesoria');
            $fecha = $request->input('fecha');

            // Busca la asesoría en la base de datos
            $asesoria = Asesoria::find($idAsesoria);
            if (!$asesoria) {
                return response()->json(['message' => 'La asesoría no existe'], 404);
            }

            // Obtiene el documento del emprendedor relacionado con la asesoría
            $docEmprendedor = $asesoria->doc_emprendedor;
            // Busca al emprendedor en la base de datos
            $emprendedor = Emprendedor::where('documento', $docEmprendedor)->first();
            if (!$emprendedor) {
                return response()->json(['message' => 'El emprendedor no existe'], 404);
            }
            $destinatario = $emprendedor;

            // Busca la relación entre la asesoría y el asesor
            $asesorxasesor = AsesoriaxAsesor::where('id_asesoria', $idAsesoria)->first();
            // if (!$asesorxasesor) {
            //     return response()->json(['message' => 'La asesoría no fue encontrada en asesoría por asesor'], 404);
            // }

            // Obtiene el ID del asesor asignado
            $id_asesorAsignado = $asesorxasesor->id_asesor;
            // Busca al asesor en la base de datos
            $asesor = Asesor::find($id_asesorAsignado);

            // Verifica si ya existe un horario para la asesoría
            $existingHorario = HorarioAsesoria::where('id_asesoria', $idAsesoria)->first();
            if ($existingHorario) {
                return response()->json(['message' => 'La asesoría ya tiene una fecha asignada'], 400);
            }

            // Crea un nuevo horario de asesoría
            $horarioAsesoria = HorarioAsesoria::create([
                'observaciones' => $request->input('observaciones') ?  $request->input('observaciones') : "Ninguna observación",
                'fecha' => $request->input('fecha'),
                'estado' => "Pendiente",
                'id_asesoria' => $request->input('id_asesoria'),
            ]);

            // Carga la relación de autenticación del emprendedor
            $destinatario->load('auth');
            // Envía un correo al emprendedor con la notificación del horario asignado
            if ($destinatario->auth && $destinatario->auth->email) {
                Mail::to($destinatario->auth->email)->send(new NotificacionAsesoriaEmprendedor($destinatario, $asesoria,  $asesor, $emprendedor, $horarioAsesoria));
            }

            // Devuelve una respuesta indicando que se ha asignado un horario a la asesoría
            return response()->json(['message' => 'Se le ha asignado un horario a su Asesoría'], 201);
        } catch (Exception $e) {
            // Manejo de errores: devuelve un mensaje de error y el código de estado 500
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    public function editarAsignacionAsesoria(Request $request)
    {
        // Verifica si el usuario autenticado tiene rol de orientador (3) o asesor (4)
        if (Auth::user()->id_rol != 3 && Auth::user()->id_rol != 4) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403); // Respuesta de error si no tiene permisos
        }

        // Busca la asignación de asesoría existente en la base de datos
        $asignacion = Asesoriaxasesor::where('id_asesoria', $request->input('id_asesoria'))->first();
        if (!$asignacion) {
            return response()->json(['message' => 'La asignación no existe en el sistema'], 404); // Respuesta si la asignación no se encuentra
        }

        // Busca el asesor en la base de datos usando el ID proporcionado
        $asesor = Asesor::find($request->input('id_asesor'));
        if (!$asesor) {
            return response()->json(['message' => 'El asesor no existe en el sistema'], 404); // Respuesta si el asesor no se encuentra
        }

        // Actualiza el ID del asesor en la asignación
        $asignacion->id_asesor = $request->input('id_asesor');
        $asignacion->save(); // Guarda los cambios en la base de datos

        // Devuelve una respuesta indicando que la actualización fue exitosa
        return response()->json(['message' => 'Se ha actualizado el asesor para esta asignación'], 200);
    }

    public function traerAsesoriasPorEmprendedor(Request $request)
    {
        // Verifica si el usuario autenticado tiene el rol de emprendedor (5)
        if (Auth::user()->id_rol != 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401); // Respuesta de error si no tiene permisos
        }

        // Obtiene los datos de entrada del documento del emprendedor y la asignación
        $documento = $request->input('documento');
        $asignacion = $request->input('asignacion');

        // Crea una consulta para obtener las asesorías relacionadas con el emprendedor
        $query = DB::table('asesoria as o')
            ->leftJoin('asesoriaxasesor as a', 'o.id', '=', 'a.id_asesoria') // Une asesorías con asignaciones a asesores
            ->leftJoin('asesor as e', 'a.id_asesor', '=', 'e.id') // Une asignaciones con asesores
            ->leftJoin('aliado as ali', 'ali.id', '=', 'o.id_aliado') // Une asesorías con aliados
            ->leftJoin('emprendedor as em', 'o.doc_emprendedor', '=', 'em.documento') // Une asesorías con emprendedores
            ->leftJoin('horarioasesoria as hr', 'o.id', '=', 'hr.id_asesoria') // Une asesorías con horarios
            ->where('em.documento', '=', $documento) // Filtra por el documento del emprendedor
            ->where('o.asignacion', '=', $asignacion) // Filtra por el estado de asignación
            ->orderBy('o.fecha', 'desc'); // Ordena los resultados por fecha de solicitud

        // Define qué columnas seleccionar dependiendo de si hay una asignación
        if ($asignacion) {
            $query->select(
                'o.id as id_asesoria',
                'o.Nombre_sol',
                'o.notas',
                'o.fecha as fecha_solicitud',
                'ali.nombre',
                'a.id_asesor',
                DB::raw('CONCAT(e.nombre, " ", e.apellido) as Asesor'), // Concatenar nombre y apellido del asesor
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
                DB::raw('IFNULL(ali.nombre, "Orientador - En espera de redireccionamiento") as nombre') // Manejo de nulos
            );
        }

        // Ejecuta la consulta y obtiene los resultados
        $asesorias = $query->get();

        // Devuelve los resultados en formato JSON
        return response()->json($asesorias);
    }


    public function traerasesoriasorientador(Request $request)
    {
        // Verifica si el usuario autenticado tiene el rol de orientador (2)
        if (Auth::user()->id_rol != 2) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403); // Respuesta de error si no tiene permisos
        }

        // Obtiene el parámetro 'pendiente' de la solicitud
        $Asignado = $request->input('pendiente');

        // Realiza la consulta para obtener las asesorías asignadas a orientadores
        $asesorias = Asesoria::with(['emprendedor.auth'])
            ->where('isorientador', true) // Filtra por asesorías que son de orientadores
            ->when($Asignado, function ($query) {
                $query->whereNull('id_aliado'); // Si 'pendiente' es verdadero, busca asesorías sin aliado
            }, function ($query) {
                $query->whereNotNull('id_aliado'); // Si no, busca asesorías que tienen un aliado
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
                // Si hay un aliado redirigido, lo agrega al resultado
                if ($asesoria->aliado && $asesoria->aliado->nombre) {
                    $data['aliado_redirigido'] = $asesoria->aliado->nombre;
                }
                return $data;
            });

        // Devuelve los resultados en formato JSON
        return response()->json($asesorias);
    }

    public function asignarAliado(Request $request, $idAsesoria)
    {
        // Captura el nombre del aliado desde la solicitud
        $nombreAliado = $request->input('nombreAliado');

        // Busca la asesoría por su ID
        $asesoria = Asesoria::find($idAsesoria);
        if (!$asesoria) {
            return response()->json(['message' => 'Asesoría no encontrada'], 404);
        }
        $destinatario = null;
        $doc_emprendedor = $asesoria->doc_emprendedor;

        // Busca al emprendedor por su documento
        $emprendedor = Emprendedor::find($doc_emprendedor);
        if (!$emprendedor) {
            return response()->json(['message' => 'Emprendedor no encontrado'], 404);
        }

        // Busca al aliado por su nombre
        $aliado = Aliado::where('nombre', $nombreAliado)->first();
        if (!$aliado) {
            return response()->json(['message' => 'Aliado no encontrado'], 404);
        }

        // Asigna el aliado a la asesoría y guarda los cambios
        $asesoria->id_aliado = $aliado->id;
        $asesoria->save();

        $destinatario = $aliado;

        // Carga la relación de autenticación del aliado
        $destinatario->load('auth');
        if ($destinatario->auth && $destinatario->auth->email) {
            // Envía un correo de notificación al aliado
            Mail::to($destinatario->auth->email)->send(new NotificacionesAsesoriaAliado($destinatario, $asesoria, $emprendedor));
        }

        // Retorna una respuesta indicando que el aliado fue asignado correctamente
        return response()->json(['message' => 'Aliado asignado correctamente'], 200);
    }

    public function MostrarAsesorias($aliadoId, $asignacion)
    {
        // Verifica si el usuario tiene permisos (rol 3)
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403);
        }

        // Busca al aliado por su ID
        $aliado = Aliado::find($aliadoId);

        if (!$aliado) {
            return response()->json(['message' => 'No se encontró ningún aliado con este ID'], 404);
        }

        // Obtiene las asesorías asociadas al aliado y su estado de asignación
        $asesorias = Asesoria::with(['emprendedor', 'asesoriaxAsesor.asesor', 'horarios'])
            ->where('id_aliado', $aliado->id)
            ->where('asignacion', $asignacion)
            ->get()
            ->map(function ($asesoria) {
                // Obtiene el primer asesor asignado y el primer horario de la asesoría
                $asesor = $asesoria->asesoriaxAsesor->first() ? $asesoria->asesoriaxAsesor->first()->asesor : null;
                $horario = $asesoria->horarios->first();

                // Prepara los datos a retornar
                $data = [
                    'id_asesoria' => $asesoria->id,
                    'Nombre_sol' => $asesoria->Nombre_sol,
                    'notas' => $asesoria->notas,
                    'fecha_solicitud' => $asesoria->fecha,
                    'Emprendedor' => $asesoria->emprendedor ? $asesoria->emprendedor->nombre . ' ' . $asesoria->emprendedor->apellido : null,
                ];

                // Verifica si hay horario asignado
                if ($horario && $horario->fecha) {
                    $data['Asesor'] = $asesor ? $asesor->nombre . ' ' . $asesor->apellido : null;
                    $data['fecha_horario'] = $horario->fecha;
                    $data['estado'] = $horario->estado;
                    $data['observaciones_asesor'] = $horario->observaciones;
                } else if ($asesor) {
                    // Si no hay horario, pero hay asesor
                    $data['Asesor'] = $asesor ? $asesor->nombre . ' ' . $asesor->apellido : null;
                    $data['mensaje'] = 'El asesor aún no ha asignado horario';
                }

                return $data; // Retorna los datos de la asesoría
            });

        return response()->json($asesorias); // Retorna la lista de asesorías
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function traerAsesoriasParaAliado(Request $request)
    {
        // Verifica si el usuario tiene permisos (rol 3)
        if (Auth::user()->id_rol != 3) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }

        // Obtiene el ID del aliado del usuario autenticado
        $idAliado = Auth::user()->aliado->id;  // Asumiendo que el usuario autenticado tiene una relación con el modelo Aliado
        $estado = $request->input('estado', 'pendiente'); // Obtiene el estado de la asesoría, por defecto 'pendiente'

        // Busca las asesorías asociadas al aliado y que tengan el estado especificado
        $asesorias = Asesoria::with(['emprendedor', 'horarios'])
            ->where('id_aliado', $idAliado)
            ->whereHas('horarios', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->get();

        // Retorna las asesorías en formato JSON
        return response()->json($asesorias);
    }

    public function listarasesoresdisponibles($idaliado)
    {
        // Verifica si el usuario tiene permisos (rol 3)
        if (Auth::user()->id_rol != 3) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción'
            ], 403); // Respuesta 403 si no tiene permisos
        }

        // Consulta para obtener los asesores disponibles
        $asesores = Asesor::selectRaw(
            'asesor.id as id_asesor,
            CONCAT(asesor.nombre, " ",asesor.apellido) as nombre_completo,
            MAX(horarioasesoria.fecha) as ultima_fecha_asesoria,
            CONCAT(
                TIMESTAMPDIFF(DAY, MAX(horarioasesoria.fecha), NOW()), " días con ", 
                TIMESTAMPDIFF(HOUR, MAX(horarioasesoria.fecha), NOW()) % 24, " horas"
            ) as tiempo_desde_ultima_asesoria'
        )
            ->leftJoin('users', 'asesor.id_autentication', '=', 'users.id') // Une la tabla de usuarios para filtrar por estado
            ->leftJoin('asesoriaxasesor', 'asesoriaxasesor.id_asesor', '=', 'asesor.id') // Une la tabla de asesorías por asesor
            ->leftJoin('horarioasesoria', 'asesoriaxasesor.id_asesoria', '=', 'horarioasesoria.id_asesoria') // Une la tabla de horarios
            ->where('asesor.id_aliado', $idaliado) // Filtra por ID de aliado
            ->whereRaw('users.estado = true') // Solo incluye usuarios activos
            ->groupBy('asesor.id', 'nombre_completo') // Agrupa por ID y nombre del asesor
            ->get();

        // Retorna la lista de asesores disponibles
        return $asesores;
    }
}

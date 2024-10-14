<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionesAsesoriaAliado;
use App\Models\Aliado;
use App\Models\Asesoria;
use App\Models\Emprendedor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\AsesoriaService;

class AsesoriasController extends Controller
{
    protected $asesoriaService;

    public function __construct(AsesoriaService $asesoriaService)
    {
        $this->asesoriaService = $asesoriaService;
    }

    //Función para guardar o crear asesorias emprendedor
    public function guardarAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            $data = $request->only([
                'nombre',
                'notas',
                'isorientador',
                'asignacion',
                'fecha',
                'nom_aliado',
                'doc_emprendedor'
            ]);

            $mensaje = $this->asesoriaService->guardarAsesoria($data);

            return response()->json(['message' => $mensaje], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    //Función para asignar asesoria a los asesores del aliado
    public function asignarAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
            }

            $data = $request->only(['id_asesoria', 'id_asesor']);
            $mensaje = $this->asesoriaService->asignarAsesoria($data);

            return response()->json(['message' => $mensaje], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    //Función para que el asesor asigne un horario a la asesoria
    public function definirHorarioAsesoria(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 4) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
            }

            $data = $request->only(['id_asesoria', 'fecha', 'observaciones']);
            $mensaje = $this->asesoriaService->definirHorarioAsesoria($data);

            return response()->json(['message' => $mensaje], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    //Función para mostrar las asesorias de cada emprendedor
    public function traerAsesoriasPorEmprendedor(Request $request)
    {
        try {
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
            }

            $documento = $request->input('documento');
            $asignacion = $request->input('asignacion');

            $asesorias = $this->asesoriaService->obtenerAsesoriasPorEmprendedor($documento, $asignacion);

            return response()->json($asesorias, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }


    //Función para mostrar las asesorías al orientador
    public function traerAsesoriasOrientador(Request $request)
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

    //Función para asignar las asesorias a los aliados
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

    //Función para mostrar las asesorías de un aliado
    public function mostrarAsesoriasAliado($aliadoId, $asignacion)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
            }

            $asesorias = $this->asesoriaService->obtenerAsesoriasPorAliado($aliadoId, $asignacion);

            return response()->json($asesorias, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    

    public function listarAsesoresDisponibles($idAliado)
    {
        try {
            if (Auth::user()->id_rol != 3) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
            }

            $asesores = $this->asesoriaService->listarAsesoresDisponibles($idAliado);

            return response()->json($asesores, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}

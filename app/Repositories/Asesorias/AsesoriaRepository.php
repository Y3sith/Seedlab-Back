<?php

namespace App\Repositories\Asesorias;

use App\Models\Aliado;
use App\Models\Asesor;
use App\Models\Asesoria;
use App\Models\AsesoriaxAsesor;
use App\Models\Emprendedor;
use App\Models\HorarioAsesoria;
use App\Models\Orientador;
use Exception;
use Illuminate\Support\Facades\DB;

class AsesoriaRepository implements AsesoriaRepositoryInterface
{
    //Función para gestionar las asesorias del aliado
    public function gestionarAsesoria(int $id_asesoria, string $accion)
    {
        $asesoria = Asesoria::find($id_asesoria);
        if (!$asesoria) {
            throw new Exception('Asesoría no encontrada');
        }

        if ($accion === 'rechazar') {
            $asesoria->id_aliado = null;
            $asesoria->isorientador = true;
            $asesoria->save();
            return 'Asesoría rechazada correctamente';
        } else {
            throw new Exception('Acción no válida');
        }
    }

    //Crear asesoria Emprendedor
    public function crearAsesoriaEmprendedor(array $data)
    {
        return Asesoria::create($data);
    }

    //Fución que busca emprendedor por documento
    public function encontrarEmprendedor($docEmprendedor)
    {
        return Emprendedor::find($docEmprendedor);
    }

    //Función que busca Aliado por nombre
    public function encontrarAliadoPorNombre($nombre)
    {
        return Aliado::where('nombre', $nombre)->first();
    }

    //Función que busca asesoria por su id
    public function obtenerAsesoriaPorId($id)
    {
        return Asesoria::find($id);
    }

    //Función que busca asesorias asignadas a un asesor
    public function verificarAsesoriaAsignada($idAsesoria)
    {
        return AsesoriaxAsesor::where('id_asesoria', $idAsesoria)->first();
    }

    //Función que asigna asesorias a un asesor
    public function asignarAsesor($data)
    {
        return Asesoriaxasesor::create($data);
    }

    public function actualizarEstadoAsesoria($idAsesoria, $estado)
    {
        $asesoria = $this->obtenerAsesoriaPorId($idAsesoria);
        $asesoria->asignacion = $estado;
        $asesoria->save();
        return $asesoria;
    }

    //Función para traer los asesores por id
    public function obtenerAsesorPorId($idAsesor)
    {
        return Asesor::find($idAsesor);
    }

    //Función para traer las asesorias de un asesor
    public function obtenerAsesorPorAsesoriaId($idAsesoria)
    {
        $asignacion = AsesoriaxAsesor::where('id_asesoria', $idAsesoria)->first();
        return $asignacion ? Asesor::find($asignacion->id_asesor) : null;
    }

    
    public function verificarHorarioExistente($idAsesoria)
    {
        return HorarioAsesoria::where('id_asesoria', $idAsesoria)->first();
    }

    public function crearHorarioAsesoria(array $data)
    {
        return HorarioAsesoria::create($data);
    }

    public function obtenerAsignacionPorAsesoriaId($idAsesoria)
    {
        return AsesoriaxAsesor::where('id_asesoria', $idAsesoria)->first();
    }

    //Función para traer asesorias por documento del emprendedor
    public function obtenerAsesoriasPorEmprendedor($documento, $asignacion)
    {
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

        return $query->get();
    }

    //Función para traer asesorias por el id del aliado
    public function obtenerAsesoriasPorAliado($aliadoId, $asignacion)
    {
        return Asesoria::with(['emprendedor', 'asesoriaxAsesor.asesor', 'horarios'])
            ->where('id_aliado', $aliadoId)
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
                } elseif ($asesor) {
                    $data['Asesor'] = $asesor ? $asesor->nombre . ' ' . $asesor->apellido : null;
                    $data['mensaje'] = 'El asesor aún no ha asignado horario';
                }

                return $data;
            });
    }

    public function obtenerAsesoresDisponiblesPorAliado($idAliado)
    {
        return Asesor::selectRaw(
            'asesor.id as id_asesor,
            CONCAT(asesor.nombre, " ", asesor.apellido) as nombre_completo,
            MAX(horarioasesoria.fecha) as ultima_fecha_asesoria,
            CONCAT(
                TIMESTAMPDIFF(DAY, MAX(horarioasesoria.fecha), NOW()), " días con ", 
                TIMESTAMPDIFF(HOUR, MAX(horarioasesoria.fecha), NOW()) % 24, " horas"
            ) as tiempo_desde_ultima_asesoria'
        )
            ->leftJoin('users', 'asesor.id_autentication', '=', 'users.id')
            ->leftJoin('asesoriaxasesor', 'asesoriaxasesor.id_asesor', '=', 'asesor.id')
            ->leftJoin('horarioasesoria', 'asesoriaxasesor.id_asesoria', '=', 'horarioasesoria.id_asesoria')
            ->where('asesor.id_aliado', $idAliado)
            ->where('users.estado', true)
            ->groupBy('asesor.id', 'nombre_completo')
            ->get();
    }

    public function obtenerOrientadoresActivos()
    {
        return Orientador::whereHas('auth', function ($query) {
            $query->where('estado', 1);
        })->get();
    }

    public function obtenerUltimaAsesoriaConOrientador()
    {
        return Asesoria::where('isorientador', true)
            ->orderBy('id', 'desc')
            ->first();
    }
}

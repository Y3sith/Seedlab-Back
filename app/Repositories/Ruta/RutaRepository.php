<?php

namespace App\Repositories\Ruta;

use App\Models\ContenidoLeccion;
use App\Models\Empresa;
use App\Models\Ruta;
use App\Repositories\Ruta\RutaRepositoryInterface;
use Carbon\Carbon;

class RutaRepository implements RutaRepositoryInterface
{
    public function obtenerRutasPorEstado($estado)
    {
        return Ruta::where('estado', $estado)
            ->get(['id', 'nombre', 'fecha_creacion', 'estado'])
            ->map(function ($ruta) {
                return [
                    'id' => $ruta->id,
                    'nombre' => $ruta->nombre,
                    'fecha_creacion' => $ruta->fecha_creacion,
                    'estado' => $ruta->estado == 1 ? 'Activo' : 'Inactivo',
                ];
            });
    }

    public function obtenerRutaPorId($id)
    {
        $ruta = Ruta::where('id', $id)
            ->select('id', 'nombre', 'fecha_creacion', 'estado')
            ->first();

        return $ruta ? [
            'id' => $ruta->id,
            'nombre' => $ruta->nombre,
            'fecha_creacion' => $ruta->fecha_creacion,
            'estado' => $ruta->estado == 1 ? 'Activo' : 'Inactivo',
        ] : null;
    }

    public function crearRuta(array $data)
    {
        return Ruta::create([
            "nombre" => $data['nombre'],
            "fecha_creacion" => Carbon::now(),
            "estado" => 1, // Estado por defecto: activo
        ]);
    }

    public function obtenerRutasActivas()
    {
        return Ruta::where('estado', 1)->select('id')->get();
    }

    public function obtenerRutaPorNombre($nombreRuta){
        return Ruta::where('nombre', $nombreRuta)->first();
    }

    public function obtenerRutas()
    {
        return Ruta::where('estado', 1)->get();
    }

    public function actualizarRuta($id, array $data)
    {
        $ruta = Ruta::find($id);

        if (!$ruta) {
            return null;
        }

        $ruta->update($data);

        return $ruta;
    }

    public function desactivarRuta($id)
    {
        $ruta = Ruta::find($id);

        if (!$ruta) {
            return null;
        }

        $ruta->update(['estado' => 0]);

        return $ruta;
    }

    public function obtenerActividadesConEstado($idRuta, $estado)
    {
        $ruta = Ruta::find($idRuta);

        if (!$ruta) {
            return null;
        }

        return $ruta->actividades()
            ->where('estado', $estado)
            ->select('id', 'nombre', 'id_ruta', 'estado', 'id_aliado')
            ->with(['aliado:id,nombre'])
            ->get()
            ->map(function ($actividad) {
                return [
                    'id' => $actividad->id,
                    'nombre' => $actividad->nombre,
                    'id_ruta' => $actividad->id_ruta,
                    'estado' => $actividad->estado == 1 ? 'Activo' : 'Inactivo',
                    'id_aliado' => $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado'
                ];
            });
    }

    public function obtenerActividadesPorRutaYAliado($idRuta, $idAliado, $estado)
    {
        $ruta = Ruta::find($idRuta);

        if (!$ruta) {
            return null;
        }

        return $ruta->actividades()
            ->where('estado', $estado)
            ->where('id_aliado', $idAliado)
            ->select('id', 'nombre', 'id_ruta', 'estado', 'id_aliado')
            ->with([
                'aliado:id,nombre',
                'nivel:id,id_asesor,id_actividad',
                'nivel.asesor:id,nombre'
            ])
            ->get()
            ->map(function ($actividad) {
                $primerAsesor = $actividad->nivel->map(function ($nivel) {
                    return $nivel->asesor ? $nivel->asesor->nombre : 'Ninguno';
                })->first() ?? 'Ninguno';

                return [
                    'id' => $actividad->id,
                    'nombre' => $actividad->nombre,
                    'id_ruta' => $actividad->id_ruta,
                    'estado' => $actividad->estado == 1 ? 'Activo' : 'Inactivo',
                    'id_aliado' => $actividad->aliado ? $actividad->aliado->nombre : 'Sin aliado',
                    'id_asesor' => $primerAsesor
                ];
            });
    }

    public function obtenerActividadesPorNivelYAsesor($idRuta, $idAsesor, $estado)
    {
        $ruta = Ruta::where('id', $idRuta)
            ->with(['actividades' => function ($query) use ($estado, $idAsesor) {
                $query->where('estado', $estado)
                    ->whereHas('nivel', function ($nivelQuery) use ($idAsesor) {
                        $nivelQuery->where('id_asesor', $idAsesor);
                    })
                    ->with(['aliado:id,nombre', 'nivel.asesor:id,nombre']);
            }])
            ->first();

        return $ruta ? $ruta->actividades->map(function ($actividad) use ($idAsesor) {
            $asesorNombre = $actividad->nivel->firstWhere('id_asesor', $idAsesor)?->asesor->nombre ?? 'Ninguno';
            return [
                'id' => $actividad->id,
                'nombre' => $actividad->nombre,
                'estado' => $actividad->estado ? 'Activo' : 'Inactivo',
                'id_aliado' => $actividad->aliado->nombre ?? 'Sin aliado',
                'id_asesor' => $asesorNombre,
            ];
        }) : null;
    }

    public function obtenerRutaConActividades($idRuta)
    {
        $ruta = Ruta::where('id', $idRuta)
            ->with([
                'actividades' => function ($query) {
                    $query->where('estado', 1);
                },
                'actividades.nivel.lecciones.contenidoLecciones',
                'actividades.aliado'
            ])
            ->first();

        return $ruta;
    }

    public function obtenerContenidoPorId($id)
    {
        return ContenidoLeccion::findOrFail($id);
    }

    public function obtenerRutaConRelaciones($id)
    {
        return Ruta::where('id', $id)->with([
            'actividades' => function ($query) {
                $query->select('id', 'id_ruta', 'nombre', 'id_aliado');
            },
            'actividades.nivel' => function ($query) {
                $query->select('id', 'id_actividad', 'nombre', 'id_asesor');
            },
            'actividades.nivel.lecciones' => function ($query) {
                $query->select('id', 'id_nivel', 'nombre');
            },
            'actividades.nivel.lecciones.contenidoLecciones' => function ($query) {
                $query->select('id', 'id_leccion', 'titulo');
            },
            'actividades.aliado' => function ($query) {
                $query->select('id', 'nombre');
            }
        ])->first();
    }

    public function obtenerEmpresasPorEmprendedor($idEmprendedor)
    {
        return Empresa::where('id_emprendedor', $idEmprendedor)->get();
    }
}


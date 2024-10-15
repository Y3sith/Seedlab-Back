<?php

namespace App\Repositories\Actividad;

use App\Models\Actividad;
use App\Models\TipoDato;

class ActividadRepository implements ActividadRepositoryInterface
{
    public function obtenerTodas()
    {
        return Actividad::all();
    }

    public function obtenerPorId($id)
    {
        return Actividad::find($id);
    }

    public function obtenerPorAliado($idAliado)
    {
        return Actividad::where('id_aliado', $idAliado)
            ->select('id', 'nombre', 'descripcion', 'fuente', 'id_tipo_dato', 'id_asesor', 'id_ruta')
            ->get();
    }

    public function obtenerActividadPorParametros(array $data)
    {
        return Actividad::where($data)->first();
    }

    public function crearActividad(array $data)
    {
        return Actividad::create($data);
    }

    public function actualizarActividad($id, array $data)
    {
        $actividad = $this->obtenerPorId($id);
        if ($actividad) {
            $actividad->update($data);
            return $actividad;
        }
        return null;
    }

    public function cambiarEstado($id, $estado)
    {
        $actividad = $this->obtenerPorId($id);
        if ($actividad) {
            $actividad->update(['estado' => $estado]);
            return $actividad;
        }
        return null;
    }

    public function obtenerConRelaciones($id)
    {
        return Actividad::with('nivel.lecciones.contenidoLecciones')
            ->where('id', $id)
            ->first();
    }

    public function obtenerActividadesPorAliado($idAliado)
    {
        return Actividad::where('id_aliado', $idAliado)
            ->select('id', 'nombre', 'descripcion', 'fuente', 'id_tipo_dato', 'id_asesor', 'id_ruta')
            ->get();
    }


    public function obtenerActividadPorId($id)
    {
        return Actividad::find($id);
    }

    public function obtenerTiposDatos()
    {
        return TipoDato::get(['id', 'nombre']);
    }
}

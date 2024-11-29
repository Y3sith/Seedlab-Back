<?php

namespace App\Repositories\Actividad;

use App\Models\Actividad;
use App\Models\TipoDato;

class ActividadRepository implements ActividadRepositoryInterface
{
    // Obtiene todas las actividades registradas en la base de datos.
    public function obtenerTodas()
    {
        return Actividad::all();
    }

    // Busca una actividad específica por su ID y la devuelve.
    public function obtenerPorId($id)
    {
        return Actividad::find($id);
    }

    // Obtiene todas las actividades asociadas a un aliado específico, basándose en el ID del aliado.
    public function obtenerPorAliado($idAliado)
    {
        return Actividad::where('id_aliado', $idAliado)
            ->select('id', 'nombre', 'descripcion', 'fuente', 'id_tipo_dato', 'id_asesor', 'id_ruta')
            ->get();
    }

    // Busca y devuelve la primera actividad que coincida con los parámetros dados en el array asociativo.
    public function obtenerActividadPorParametros(array $data)
    {
        return Actividad::where($data)->first();
    }

    // Crea una nueva actividad con los datos proporcionados en el array.
    public function crearActividad(array $data)
    {
        return Actividad::create($data);
    }

    // Busca la actividad por su ID, si la encuentra, actualiza sus datos con los valores proporcionados en el array.
    // Retorna la actividad actualizada o null si no se encontró.
    public function actualizarActividad($id, array $data)
    {
        $actividad = $this->obtenerPorId($id);
        if ($actividad) {
            $actividad->update($data);
            return $actividad;
        }
        return null;
    }

    // Cambia el estado de una actividad específica ( activo o inactivo).
    // Busca la actividad por su ID y actualiza su columna 'estado'. Devuelve la actividad actualizada o null si no se encontró.
    public function cambiarEstado($id, $estado)
    {
        $actividad = $this->obtenerPorId($id);
        if ($actividad) {
            $actividad->update(['estado' => $estado]);
            return $actividad;
        }
        return null;
    }

    // Obtiene una actividad por su ID, incluyendo sus relaciones con niveles, lecciones y contenidos de lecciones.
    // Usa Eloquent's 'with' para cargar relaciones relacionadas en un solo query.
    public function obtenerConRelaciones($id)
    {
        return Actividad::with('nivel.lecciones.contenidoLecciones')
            ->where('id', $id)
            ->first();
    }

    // Obtiene todas las actividades asociadas a un aliado, 
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

    // Devuelve todos los tipos de datos disponibles en la tabla TipoDato
    public function obtenerTiposDatos()
    {
        return TipoDato::get(['id', 'nombre']);
    }
}

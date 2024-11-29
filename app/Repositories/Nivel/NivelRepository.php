<?php

namespace App\Repositories\Nivel;

use App\Models\Nivel;

class NivelRepository implements NivelRepositoryInterface
{
    public function encontrarPorNombreYActividad($nombre, $idActividad)
    {
        return Nivel::where('nombre', $nombre)
            ->where('id_actividad', $idActividad)
            ->first();
    }

    public function crearNivel(array $data)
    {
        return Nivel::create($data);
    }

    public function listarNiveles()
    {
        return Nivel::select('id', 'nombre')->get();
    }

    public function nivelesPorActividad($idActividad)
    {
        return Nivel::where('id_actividad', $idActividad)
            ->select('id', 'nombre', 'id_asesor')
            ->get();
    }

    public function nivelesPorActividadYAsesor($idActividad, $idAsesor)
    {
        return Nivel::where('id_actividad', $idActividad)
            ->where('id_asesor', $idAsesor)
            ->with('asesor:id,nombre')
            ->get();
    }

    public function encontrarPorId($id)
    {
        return Nivel::find($id);
    }

    public function actualizarNivel($id, array $data)
    {
        $nivel = $this->encontrarPorId($id);
        if ($nivel) {
            $nivel->update($data);
            return $nivel;
        }
        return null;
    }
}

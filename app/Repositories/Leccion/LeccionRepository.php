<?php

namespace App\Repositories\Leccion;

use App\Models\Leccion;

class LeccionRepository implements LeccionRepositoryInterface
{
    public function encontrarPorNombreYNivel($nombre, $idNivel)
    {
        return Leccion::where('nombre', $nombre)
            ->where('id_nivel', $idNivel)
            ->first();
    }

    public function crearLeccion(array $data)
    {
        return Leccion::create($data);
    }

    public function obtenerLeccionesPorNivel($idNivel)
    {
        return Leccion::where('id_nivel', $idNivel)
            ->select('id', 'nombre')
            ->get();
    }

    public function encontrarPorId($id)
    {
        return Leccion::find($id);
    }

    public function actualizarLeccion($id, array $data)
    {
        $leccion = $this->encontrarPorId($id);
        if ($leccion) {
            $leccion->update($data);
            return $leccion;
        }
        return null;
    }
}

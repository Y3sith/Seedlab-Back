<?php

namespace App\Repositories\Leccion;

interface LeccionRepositoryInterface
{
    public function encontrarPorNombreYNivel($nombre, $idNivel);
    public function crearLeccion(array $data);
    public function obtenerLeccionesPorNivel($idNivel);
    public function encontrarPorId($id);
    public function actualizarLeccion($id, array $data);
}

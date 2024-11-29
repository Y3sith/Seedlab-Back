<?php

namespace App\Repositories\Nivel;

interface NivelRepositoryInterface
{
    public function encontrarPorNombreYActividad($nombre, $idActividad);
    public function crearNivel(array $data);
    public function listarNiveles();
    public function nivelesPorActividad($idActividad);
    public function nivelesPorActividadYAsesor($idActividad, $idAsesor);
    public function encontrarPorId($id);
    public function actualizarNivel($id, array $data);
}

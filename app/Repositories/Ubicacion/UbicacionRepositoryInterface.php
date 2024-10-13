<?php

namespace App\Repositories\Ubicacion;

interface UbicacionRepositoryInterface{
    public function obtenerDepartamentos();
    public function obtenerMunicipiosPorDepartamento($idDepartamento);

    public function obtenerDepartamentoPorId($id);
}
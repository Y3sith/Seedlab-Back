<?php

namespace App\Repositories\Ubicacion;

use App\Models\Departamento;
use App\Models\Municipio;

class UbicacionRepository implements UbicacionRepositoryInterface
{

    public function obtenerDepartamentos()
    {
        return Departamento::select('id', 'name')->get();
    }

    public function obtenerMunicipiosPorDepartamento($idDepartamento)
    {
        return Municipio::where('id_departamento', $idDepartamento)
            ->select('id', 'nombre')
            ->get();
    }

    public function obtenerDepartamentoPorId($id)
    {
        return Departamento::where("id", $id)->first();
    }
}

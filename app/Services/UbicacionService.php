<?php

namespace App\Services;

use App\Repositories\Ubicacion\UbicacionRepository;

class UbicacionService
{
    protected $ubicacionRepository;

    public function __construct(UbicacionRepository $ubicacionRepository)
    {
        $this->ubicacionRepository = $ubicacionRepository;
    }

    public function listarDepartamentos()
    {
        return $this->ubicacionRepository->obtenerDepartamentos();
    }

    public function listarMunicipiosPorDepartamento($idDepartamento)
    {
        $departamento = $this->ubicacionRepository->obtenerDepartamentoPorId($idDepartamento);

        if (!$departamento) {
            throw new \Exception('Departamento no encontrado');
        }

        return $this->ubicacionRepository->obtenerMunicipiosPorDepartamento($idDepartamento);
    }
}

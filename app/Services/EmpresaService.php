<?php

namespace App\Services;

use App\Repositories\Empresa\EmpresaRepositoryInterface;

class EmpresaService
{

    protected $empresaRepository;

    public function __construct(EmpresaRepositoryInterface $empresaRepository)
    {
        $this->empresaRepository = $empresaRepository;
    }

    public function obtenerEmpresas()
    {
        return $this->empresaRepository->obtenerEmpresas();
    }

    public function obtenerEmpresasPorEmprendedor($docEmprendedor)
    {
        return $this->empresaRepository->obtenerEmpresasPorEmprendedor($docEmprendedor);
    }

    public function obtenerEmpresaPorIdYDocumento($idEmprendedor, $documento)
    {
        return $this->empresaRepository->obtenerEmpresaPorIdYDocumento($idEmprendedor, $documento);
    }

    public function crearEmpresa(array $data)
    {
        return $this->empresaRepository->crearEmpresa($data);
    }

    public function actualizarEmpresa($documento, array $data)
    {
        return $this->empresaRepository->actualizarEmpresa($documento, $data);
    }
}

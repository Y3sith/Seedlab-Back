<?php

namespace App\Repositories\Apoyo;

use Exception;

class ApoyoService
{
    protected $apoyoEmpresaRepository;

    public function __construct(ApoyoRepository $apoyoEmpresaRepository)
    {
        $this->apoyoEmpresaRepository = $apoyoEmpresaRepository;
    }

    public function crearApoyo(array $data){
        
        $apoyo = $this->apoyoEmpresaRepository->crearApoyoConEmpresa($data);
        return $apoyo;
    }


    public function editarApoyo($documento, array $data)
    {
        $apoyo = $this->apoyoEmpresaRepository->getApoyoxDocumento($documento);

        if (!$apoyo) {
            throw new Exception('Apoyo no encontrado');
        }

        $apoyoActualizado = $this->apoyoEmpresaRepository->actualizarPorDocumento($documento, $data);

        return $apoyoActualizado;
    }

    public function getApoyosxEmpresa($id_empresa){
        return $this->apoyoEmpresaRepository->getApoyosxEmpresa($id_empresa);
    }
}

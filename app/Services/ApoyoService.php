<?php

namespace App\Services;

use App\Repositories\Apoyo\ApoyoRepository;
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
        $apoyo = $this->apoyoEmpresaRepository->actualizarPorDocumento($documento, $data);

        if (!$apoyo) {
            throw new Exception('Apoyo no encontrado');
        }


        return $apoyo;
    }

    public function getApoyosxEmpresa($id_empresa){
        return $this->apoyoEmpresaRepository->getApoyosxEmpresa($id_empresa);
    }
}

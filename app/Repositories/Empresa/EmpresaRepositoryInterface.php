<?php

namespace App\Repositories\Empresa;


interface EmpresaRepositoryInterface{
    
    public function obtenerEmpresas();
    public function obtenerEmpresasPorEmprendedor($docEmprendedor);
    public function obtenerEmpresaPorIdYDocumento($idEmprendedor, $documento);
    public function crearEmpresa(array $data);
    public function actualizarEmpresa($documento, array $data);

    public function crearApoyo(array $data);
}
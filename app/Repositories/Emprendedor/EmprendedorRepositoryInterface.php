<?php

namespace App\Repositories\Emprendedor;


interface EmprendedorRepositoryInterface{

    public function obtenerEmpresasPorEmprendedor($id_emprendedor);
    public function encontrarEmprendedorPorDocumento($documento);
    public function actualizarEmprendedor($emprendedor, array $data);
    public function desactivarEmprendedor($documento);
    public function obtenerTiposDocumento();
}
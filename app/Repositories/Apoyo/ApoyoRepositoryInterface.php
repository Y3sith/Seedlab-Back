<?php 

namespace App\Repositories\Apoyo;


interface ApoyoRepositoryInterface{
    public function crearApoyoConEmpresa(array $data);
    public function getApoyosxEmpresa($idEmpresa);
}



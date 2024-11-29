<?php

namespace App\Repositories\Orientador;


interface OrientadorRepositoryInterface{
    public function crearOrientador(array $data);
    public function asignarAsesoriaAliado($idAsesoria, $nombreAliado);
    public function listarAliados();
    public function contarEmprendedores();
    public function mostrarOrientadores($status);
    public function editarOrientador($id, array $data);
    public function obtenerPerfil($id);
}
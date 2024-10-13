<?php

namespace App\Repositories\Aliado;

interface AliadoRepositoryInterface{
    public function traerAliadosActivos(int $status);
    public function traerAliadoxId(int $id);
    public function mostrarAliados(string $estado);
    public function getAllAliados(int $id);
    public function traerAliadoPorId(int $id);
    public function crearAliado(array $data);
    public function editarAliado(int $id, array $data);
    public function desactivarAliado(int $id);
    public function obtenerAsesoresPorAliado($id, $estado);
}
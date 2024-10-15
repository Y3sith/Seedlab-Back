<?php

namespace App\Repositories\Actividad;

interface ActividadRepositoryInterface
{
    public function obtenerTodas();
    public function obtenerPorId($id);
    public function obtenerPorAliado($idAliado);
    public function obtenerActividadPorParametros(array $data);
    public function crearActividad(array $data);
    public function actualizarActividad($id, array $data);
    public function cambiarEstado($id, $estado);
    public function obtenerConRelaciones($id);
    public function obtenerActividadesPorAliado($idAliado);
    public function obtenerActividadPorId($id);

    public function obtenerTiposDatos();
}

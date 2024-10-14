<?php

namespace App\Repositories\Asesorias;


interface AsesoriaRepositoryInterface
{
    //Gestion de asesorias aliado
    public function gestionarAsesoria(int $id_asesoria, string $accion);
    

    //Crear asesoria emprendedor
    public function crearAsesoriaEmprendedor(array $data);
    //Buscar emprendedor por documento
    public function encontrarEmprendedor($docEmprendedor);
    //Buscar aliado por nombre
    public function encontrarAliadoPorNombre($nombre);
    //Obtener siguiente orientador para la asesoria
    public function obtenerUltimaAsesoriaConOrientador();
    public function obtenerOrientadoresActivos();

    //Asiganr Asesorias Aliados
    public function obtenerAsesoriaPorId($id);
    public function verificarAsesoriaAsignada($idAsesoria);
    public function asignarAsesor($data);
    public function actualizarEstadoAsesoria($idAsesoria, $estado);
    public function obtenerAsesorPorId($idAsesor);

    //Asignar horaria asesoria por el asesor
    public function obtenerAsesorPorAsesoriaId($idAsesoria);
    public function verificarHorarioExistente($idAsesoria);
    public function crearHorarioAsesoria(array $data);

    //Traer las asesorias del emprendedor
    public function obtenerAsesoriasPorEmprendedor($documento, $asignacion);

    //Traer las asesorias del aliado
    public function obtenerAsesoriasPorAliado($aliadoId, $asignacion);

    public function obtenerAsesoresDisponiblesPorAliado($idAliado);
}

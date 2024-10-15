<?php

namespace App\Services;

use App\Repositories\Nivel\NivelRepositoryInterface;
use App\Repositories\Actividad\ActividadRepositoryInterface;
use App\Repositories\Asesor\AsesorRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionesActividadAsesor;
use Exception;

class NivelService
{
    protected $nivelRepository;
    protected $actividadRepository;
    protected $asesorRepository;

    public function __construct(
        NivelRepositoryInterface $nivelRepository,
        ActividadRepositoryInterface $actividadRepository,
        AsesorRepositoryInterface $asesorRepository
    ) {
        $this->nivelRepository = $nivelRepository;
        $this->actividadRepository = $actividadRepository;
        $this->asesorRepository = $asesorRepository;
    }

    public function crearNivel(array $data)
    {
        $existingNivel = $this->nivelRepository->encontrarPorNombreYActividad($data['nombre'], $data['id_actividad']);
        if ($existingNivel) {
            throw new Exception('Ya existe un nivel con este nombre para esta actividad');
        }

        $actividad = $this->actividadRepository->obtenerPorId($data['id_actividad']);
        if (!$actividad) {
            throw new Exception('No se pudo crear el nivel debido a que la actividad no fue encontrada');
        }

        $nivel = $this->nivelRepository->crearNivel($data);

        if (isset($data['id_asesor'])) {
            $asesor = $this->asesorRepository->buscarAsesorPorId($data['id_asesor']);
            if ($asesor && $asesor->auth && $asesor->auth->email) {
                Mail::to($asesor->auth->email)->send(
                    new NotificacionesActividadAsesor($actividad->nombre, $nivel->nombre, $asesor->nombre)
                );
            }
        }

        return $nivel;
    }

    public function listarNiveles()
    {
        return $this->nivelRepository->listarNiveles();
    }

    public function nivelesPorActividad($idActividad)
    {
        return $this->nivelRepository->nivelesPorActividad($idActividad);
    }

    public function nivelesPorActividadYAsesor($idActividad, $idAsesor)
    {
        return $this->nivelRepository->nivelesPorActividadYAsesor($idActividad, $idAsesor);
    }

    public function actualizarNivel($id, array $data)
    {
        $nivel = $this->nivelRepository->encontrarPorId($id);
        if (!$nivel) {
            throw new Exception('Nivel no encontrado');
        }

        $idAsesorAnterior = $nivel->id_asesor;
        $nivelActualizado = $this->nivelRepository->actualizarNivel($id, $data);

        if ($idAsesorAnterior != $data['id_asesor']) {
            $asesor = $this->asesorRepository->buscarAsesorPorId($data['id_asesor']);
            $actividad = $this->actividadRepository->obtenerPorId($data['id_actividad']);
            if ($asesor && $asesor->auth && $asesor->auth->email && $actividad) {
                Mail::to($asesor->auth->email)->send(
                    new NotificacionesActividadAsesor($actividad->nombre, $nivel->nombre, $asesor->nombre)
                );
            }
        }

        return $nivelActualizado;
    }
}

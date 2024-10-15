<?php

namespace App\Services;

use App\Jobs\EnviarNotificacionActividadAliado;
use App\Services\ImageService;
use App\Repositories\Actividad\ActividadRepositoryInterface;
use App\Repositories\Aliado\AliadoRepositoryInterface;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ActividadService
{
    protected $actividadRepository;
    protected $aliadoRepository;
    protected $imageService;

    public function __construct(
        ActividadRepositoryInterface $actividadRepository,
        AliadoRepositoryInterface $aliadoRepository,
        ImageService $imageService
    ) {
        $this->actividadRepository = $actividadRepository;
        $this->aliadoRepository = $aliadoRepository;
        $this->imageService = $imageService;
    }

    public function listarTodas()
    {
        return $this->actividadRepository->obtenerTodas();
    }

    public function obtenerPorId($id)
    {
        return $this->actividadRepository->obtenerPorId($id);
    }

    public function tipoDato()
    {
        return $this->actividadRepository->obtenerTiposDatos();
    }

    public function crearActividad(array $data)
    {
        $aliado = $this->aliadoRepository->traerAliadoPorId($data['id_aliado']);
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        $existingActividad = $this->actividadRepository->obtenerActividadPorParametros([
            ['nombre', $data['nombre']],
            ['descripcion', $data['descripcion']],
            ['id_tipo_dato', $data['id_tipo_dato']],
            ['id_ruta', $data['id_ruta']],
            ['id_aliado', $data['id_aliado']],
        ]);

        if ($existingActividad) {
            throw new Exception('La actividad ya existe');
        }

        if (isset($data['fuente']) && $data['fuente'] instanceof UploadedFile) {
            $folder = 'imagenes';
            $webpPath = $this->imageService->procesarImagen($data['fuente'], $folder);
            $data['fuente'] = Storage::url($webpPath);
        } else {
            throw new Exception('No se proporcionó una fuente de imagen válida');
        }

        $actividad = $this->actividadRepository->crearActividad($data);

        if ($aliado->auth && $aliado->auth->email) {
            EnviarNotificacionActividadAliado::dispatch($actividad->nombre, $aliado);
        }

        return $actividad;
    }

    public function actualizarActividad($id, array $data)
    {
        $aliado = $this->aliadoRepository->traerAliadoPorId($data['id_aliado']);
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        $actividad = $this->actividadRepository->obtenerPorId($id);
        if (!$actividad) {
            throw new Exception('Actividad no encontrada');
        }

        if (isset($data['fuente']) && $data['fuente'] instanceof UploadedFile) {
            Storage::delete(str_replace('storage', 'public', $actividad->fuente));

            $folder = 'imagenes';
            $webpPath = $this->imageService->procesarImagen($data['fuente'], $folder);
            $data['fuente'] = Storage::url($webpPath);
        }

        $this->actividadRepository->actualizarActividad($id, $data);

        if ($actividad->id_aliado != $data['id_aliado'] && $aliado->auth && $aliado->auth->email) {
            EnviarNotificacionActividadAliado::dispatch($data['nombre'], $aliado);
        }

        return $actividad;
    }

    public function activarDesactivarActividad($id)
    {
        // Obtener la actividad por su ID
        $actividad = $this->actividadRepository->obtenerPorId($id);

        // Verificar si existe
        if (!$actividad) {
            throw new Exception('Actividad no encontrada');
        }

        // Cambiar el estado de la actividad
        $nuevoEstado = !$actividad->estado;
        $actividadActualizada = $this->actividadRepository->cambiarEstado($id, $nuevoEstado);

        return $actividadActualizada;
    }

    public function obtenerActividadesPorAliado($idAliado)
    {
        return $this->actividadRepository->obtenerActividadesPorAliado($idAliado);
    }

    public function obtenerActividadConRelaciones($id)
    {
        return $this->actividadRepository->obtenerConRelaciones($id);
    }

    public function obtenerActividadPorId($id)
    {
        return $this->actividadRepository->obtenerActividadPorId($id);
    }
}

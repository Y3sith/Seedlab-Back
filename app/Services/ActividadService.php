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

    // Llama al repositorio para obtener todas las actividades de la base de datos.
    public function listarTodas()
    {
        return $this->actividadRepository->obtenerTodas();
    }

    // Llama al repositorio para obtener una actividad específica según su ID.
    public function obtenerPorId($id)
    {
        return $this->actividadRepository->obtenerPorId($id);
    }

    // Llama al repositorio para obtener los tipos de datos asociados a las actividades.
    public function tipoDato()
    {
        return $this->actividadRepository->obtenerTiposDatos();
    }

    public function crearActividad(array $data)
    {
        // Busca al aliado que va a ser asociado a la actividad.
        $aliado = $this->aliadoRepository->traerAliadoPorId($data['id_aliado']);
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        // Verifica si ya existe una actividad con los mismos parámetros.
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

        // Procesa la imagen proporcionada para la actividad.
        if (isset($data['fuente']) && $data['fuente'] instanceof UploadedFile) {
            $folder = 'imagenes';
            $webpPaths = $this->imageService->procesarImagen($data['fuente'], $folder);

            $data['fuente'] = isset($webpPaths['medium']) ? Storage::url($webpPaths['medium']) : Storage::url($webpPaths[0]);
        } else {
            throw new Exception('No se proporcionó una fuente de imagen válida');
        }

        // Crea la actividad y envía una notificación por correo al aliado asociado.
        $actividad = $this->actividadRepository->crearActividad($data);

        if ($aliado->auth && $aliado->auth->email) {
            EnviarNotificacionActividadAliado::dispatch($actividad->nombre, $aliado);
        }

        return $actividad;
    }

    public function actualizarActividad($id, array $data)
    {
        // Busca al aliado que va a ser asociado a la actividad.
        $aliado = $this->aliadoRepository->traerAliadoPorId($data['id_aliado']);
        if (!$aliado) {
            throw new Exception('Aliado no encontrado');
        }

        // Busca la actividad por ID para actualizarla.
        $actividad = $this->actividadRepository->obtenerPorId($id);
        if (!$actividad) {
            throw new Exception('Actividad no encontrada');
        }

        // Si se proporciona una nueva imagen, elimina la anterior y guarda la nueva.
        if (isset($data['fuente']) && $data['fuente'] instanceof UploadedFile) {
            Storage::delete(str_replace('storage', 'public', $actividad->fuente));

            $folder = 'imagenes';
            $webpPath = $this->imageService->procesarImagen($data['fuente'], $folder);
            $data['fuente'] = Storage::url($webpPath);
        }

        // Actualiza los datos de la actividad.
        $this->actividadRepository->actualizarActividad($id, $data);

        if ($actividad->id_aliado != $data['id_aliado'] && $aliado->auth && $aliado->auth->email) {
            EnviarNotificacionActividadAliado::dispatch($data['nombre'], $aliado);
        }

        return $actividad;
    }

    public function activarDesactivarActividad($id)
    {
        // Busca la actividad por su ID.
        $actividad = $this->actividadRepository->obtenerPorId($id);

        // Verifica si la actividad existe.
        if (!$actividad) {
            throw new Exception('Actividad no encontrada');
        }

        // Cambia el estado de la actividad (activo/inactivo).
        $nuevoEstado = !$actividad->estado;
        $actividadActualizada = $this->actividadRepository->cambiarEstado($id, $nuevoEstado);

        return $actividadActualizada;
    }

    // Llama al repositorio para obtener todas las actividades asociadas a un aliado específico.
    public function obtenerActividadesPorAliado($idAliado)
    {
        return $this->actividadRepository->obtenerActividadesPorAliado($idAliado);
    }

    // Llama al repositorio para obtener una actividad con todas sus relaciones (niveles, lecciones, etc.).
    public function obtenerActividadConRelaciones($id)
    {
        return $this->actividadRepository->obtenerConRelaciones($id);
    }

    // Llama al repositorio para obtener una actividad por su ID.
    public function obtenerActividadPorId($id)
    {
        return $this->actividadRepository->obtenerActividadPorId($id);
    }
}

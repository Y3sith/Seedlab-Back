<?php

namespace App\Services;

use App\Repositories\ContenidoLeccion\ContenidoLeccionRepository;
use Illuminate\Support\Facades\Storage;
use Exception;

class ContenidoLeccionService
{
    protected $contenidoLeccionRepository;
    protected $imageService;

    public function __construct(ContenidoLeccionRepository $contenidoLeccionRepository, ImageService $imageService)
    {
        $this->contenidoLeccionRepository = $contenidoLeccionRepository;
        $this->imageService = $imageService;
    }

    public function crearContenido(array $data, $fuenteContenido)
    {
        // Verificar si el título ya existe en la misma lección
        $existingContenido = $this->contenidoLeccionRepository->buscarPorTituloYLeccion($data['titulo'], $data['id_leccion']);

        if ($existingContenido) {
            throw new Exception('El título para esta lección ya existe');
        }

        // Procesar la fuente del contenido
        $fuente = null;

        if ($fuenteContenido) {
            $rutas = $this->imageService->procesarRutaMulti($fuenteContenido);

            // Si $rutas es un array, selecciona solo la versión que necesitas
            if (is_array($rutas)) {
                $fuente = $rutas['medium'] ?? current($rutas); // Elige 'medium' o la primera opción disponible
            } else {
                $fuente = $rutas; // Si es una sola ruta, úsala directamente
            }
        } elseif (filter_var($data['fuente_contenido'], FILTER_VALIDATE_URL)) {
            $fuente = $data['fuente_contenido'];
        } else {
            $fuente = $data['fuente_contenido'];
        }

        $data['fuente_contenido'] = $fuente;

        return $this->contenidoLeccionRepository->crearContenido($data);
    }


    public function editarContenido($id, array $data, $fuenteContenido)
    {
        $contenido = $this->contenidoLeccionRepository->obtenerPorId($id);

        if (!$contenido) {
            throw new Exception('Contenido no encontrado');
        }

        if ($fuenteContenido) {
            $fuente = $this->imageService->procesarRutaMulti($fuenteContenido);

            if ($contenido->fuente_contenido && Storage::exists(str_replace('storage', 'public', $contenido->fuente_contenido))) {
                Storage::delete(str_replace('storage', 'public', $contenido->fuente_contenido));
            }

            $data['fuente_contenido'] = $fuente;
        }

        return $this->contenidoLeccionRepository->actualizarContenido($contenido, $data);
    }

    public function obtenerTiposDeDato()
    {
        return $this->contenidoLeccionRepository->obtenerTiposDeDato();
    }

    public function obtenerContenidoPorLeccion($idLeccion)
    {
        return $this->contenidoLeccionRepository->obtenerContenidoPorLeccion($idLeccion);
    }
}

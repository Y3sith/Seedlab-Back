<?php

namespace App\Repositories\ContenidoLeccion;
use App\Models\ContenidoLeccion;


interface ContenidoLeccionRepositoryInterface {
    public function buscarPorTituloYLeccion($titulo, $idLeccion);

    public function crearContenido(array $data);

    public function obtenerPorId($id);

    public function actualizarContenido(ContenidoLeccion $contenido, array $data);

    public function obtenerTiposDeDato();

    public function obtenerContenidoPorLeccion($idLeccion);
}
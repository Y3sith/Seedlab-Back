<?php

namespace App\Repositories\ContenidoLeccion;

use App\Models\ContenidoLeccion;
use App\Models\TipoDato;
use App\Repositories\ContenidoLeccion\ContenidoLeccionRepositoryInterface;

class ContenidoLeccionRepository implements ContenidoLeccionRepositoryInterface
{
    public function buscarPorTituloYLeccion($titulo, $idLeccion)
    {
        return ContenidoLeccion::where('titulo', $titulo)
            ->where('id_leccion', $idLeccion)
            ->first();
    }

    public function crearContenido(array $data)
    {
        return ContenidoLeccion::create($data);
    }

    public function obtenerPorId($id)
    {
        return ContenidoLeccion::find($id);
    }

    public function actualizarContenido(ContenidoLeccion $contenido, array $data)
    {
        $contenido->update($data);
        return $contenido;
    }

    public function obtenerTiposDeDato()
    {
        return TipoDato::get(['id', 'nombre']);
    }

    public function obtenerContenidoPorLeccion($idLeccion)
    {
        return ContenidoLeccion::where('id_leccion', $idLeccion)->get();
    }
}

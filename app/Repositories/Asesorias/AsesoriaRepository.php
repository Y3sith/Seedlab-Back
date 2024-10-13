<?php

namespace App\Repositories\Asesorias;

use App\Models\Asesoria;
use Exception;

class AsesoriaRepository implements AsesoriaRepositoryInterface
{
    public function gestionarAsesoria(int $id_asesoria, string $accion)
    {
        $asesoria = Asesoria::find($id_asesoria);
        if (!$asesoria) {
            throw new Exception('Asesoría no encontrada');
        }

        if ($accion === 'rechazar') {
            $asesoria->id_aliado = null;
            $asesoria->isorientador = true;
            $asesoria->save();
            return 'Asesoría rechazada correctamente';
        } else {
            throw new Exception('Acción no válida');
        }
    }
}

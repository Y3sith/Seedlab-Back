<?php

namespace App\Repositories\Apoyo;

use App\Models\ApoyoEmpresa;

class ApoyoRepository implements ApoyoRepositoryInterface
{

    public function crearApoyoConEmpresa(array $data)
    {
        return ApoyoEmpresa::create([
            'documento' => $data['documento'],
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'cargo' => $data['cargo'],
            'telefono' => $data['telefono'],
            'celular' => $data['celular'],
            'email' => $data['email'],
            'id_tipo_documento' => $data['id_tipo_documento'],
            'id_empresa' => $data['id_empresa'],
        ]);
    }

    public function getApoyosxEmpresa($id_empresa)
    {
        return ApoyoEmpresa::where('id_empresa', $id_empresa)->get();
    }

    public function actualizarPorDocumento($documento, array $data)
    {
        
        $apoyo = $this->getApoyoxDocumento($documento);

        if (!$apoyo) {
            return null;
        }

        $apoyo->update($data);

        return $apoyo;
    }

    public function getApoyoxDocumento($documento)
    {
        return ApoyoEmpresa::where('documento', $documento)->first();
    }
}

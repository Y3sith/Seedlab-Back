<?php

namespace App\Repositories\Apoyo;

use App\Models\ApoyoEmpresa;

class ApoyoRepository implements ApoyoRepositoryInterface
{

    // Crea un nuevo registro de apoyo asociado a una empresa.
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

    // Obtiene todos los apoyos que pertenecen a una empresa específica.
    public function getApoyosxEmpresa($id_empresa)
    {
        return ApoyoEmpresa::where('id_empresa', $id_empresa)->get(); // Filtra apoyos por el ID de la empresa y los devuelve.
    }

    // Actualiza la información de un apoyo específico usando su documento.
    public function actualizarPorDocumento($documento, array $data)
    {
        // Obtiene el apoyo basado en su documento.
        $apoyo = $this->getApoyoxDocumento($documento);

        if (!$apoyo) {
            return null; // Retorna null si no se encuentra el apoyo.
        }
        // Actualiza los datos del apoyo.
        $apoyo->update($data);

        return $apoyo;// Devuelve el apoyo actualizado.
    }

    // Obtiene un apoyo específico basado en su documento.
    public function getApoyoxDocumento($documento)
    {
        return ApoyoEmpresa::where('documento', $documento)->first();// Busca el primer apoyo que coincida con el documento y lo devuelve.
    }
}

<?php

namespace App\Services;

use App\Repositories\Apoyo\ApoyoRepository;
use Exception;

class ApoyoService
{
    // Inyecta la dependencia del ApoyoRepository, que maneja la interacción con la base de datos.
    protected $apoyoEmpresaRepository;

    // Constructor que inicializa el repositorio de apoyo.
    public function __construct(ApoyoRepository $apoyoEmpresaRepository)
    {
        $this->apoyoEmpresaRepository = $apoyoEmpresaRepository;
    }

    // Crea un nuevo apoyo asociado a una empresa.
    public function crearApoyo(array $data){
        // Llama al repositorio para crear el apoyo con los datos proporcionados.
        $apoyo = $this->apoyoEmpresaRepository->crearApoyoConEmpresa($data);
        return $apoyo;// Retorna el apoyo creado.
    }

    // Edita un apoyo existente basado en su documento.
    public function editarApoyo($documento, array $data)
    {
        // Llama al repositorio para actualizar el apoyo identificado por su documento.
        $apoyo = $this->apoyoEmpresaRepository->actualizarPorDocumento($documento, $data);

        if (!$apoyo) {
            throw new Exception('Apoyo no encontrado');
        }


        return $apoyo;
    }

    // Obtiene todos los apoyos asociados a una empresa.
    public function getApoyosxEmpresa($id_empresa){
        // Llama al repositorio para obtener los apoyos de la empresa especificada.
        return $this->apoyoEmpresaRepository->getApoyosxEmpresa($id_empresa);
    }
}

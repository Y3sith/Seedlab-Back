<?php

namespace App\Services;

use App\Repositories\Asesor\AsesorRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCrearUsuario;
use Illuminate\Support\Facades\Hash;

class AsesorService
{
    protected $asesorRepository;

    public function __construct(AsesorRepositoryInterface $asesorRepository)
    {
        $this->asesorRepository = $asesorRepository;
    }

    public function crearAsesor(array $data, $imagenPerfil)
    {
        // Procesar la imagen de perfil
        $data['imagen_perfil'] = null;
        if ($imagenPerfil && $imagenPerfil->isValid()) {
            $data['imagen_perfil'] = Storage::url($imagenPerfil->store('fotoPerfil', 'public'));
        }

        // contraseña generada antes de hashearla
        $originalPassword = $data['password'];

        // Hashear la contraseña
        $data['password'] = Hash::make($data['password']);

        // Ordenar los parámetros según el orden del procedimiento almacenado
        $params = [
            $data['nombre'],
            $data['apellido'],
            $data['documento'],
            $data['imagen_perfil'],
            $data['celular'],
            $data['genero'],
            $data['direccion'],
            $data['aliado'], // Nombre del aliado
            $data['id_tipo_documento'],
            $data['departamento'],
            $data['municipio'],
            $data['fecha_nac'],
            $data['email'],
            $data['password'],
            $data['estado'],
        ];

        //Log::info('Parámetros enviados a sp_registrar_asesor:', $params);

        $results = $this->asesorRepository->crearAsesor($params);

        // Enviar notificación de correo
        if (!empty($results) && isset($results[0]->email)) {
            Mail::to($results[0]->email)->send(new NotificacionCrearUsuario($results[0]->email, 'Asesor', $originalPassword));
        }

        return $results[0]->mensaje;
    }



    public function actualizarAsesor($id, array $data, $imagenPerfil = null)
    {
        $asesor = $this->asesorRepository->buscarAsesorPorId($id);

        if (!$asesor) {
            throw new Exception('Asesor no encontrado');
        }

        if ($imagenPerfil && $imagenPerfil->isValid()) {
            Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));
            $data['imagen_perfil'] = Storage::url($imagenPerfil->store('fotoPerfil', 'public'));
        }

        return $this->asesorRepository->actualizarAsesor($asesor, $data);
    }

    public function obtenerAsesoriasPorId($id, $conHorario)
    {
        $asesorias = $this->asesorRepository->buscarAsesoriasPorId($id);
        return $asesorias->filter(function ($asesoria) use ($conHorario) {
            return ($conHorario === 'true' && $asesoria->horarios->isNotEmpty()) || ($conHorario === 'false' && $asesoria->horarios->isEmpty());
        });
    }

    public function obtenerAsesorConUbicacion($id)
    {
        return $this->asesorRepository->buscarAsesorConUbicacion($id);
    }
}

<?php

namespace App\Services;

use App\Repositories\Asesor\AsesorRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCrearUsuario;
use App\Models\User;
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
        $asesor = $this->asesorRepository->buscarAsesorConUbicacion($id);

        if (!$asesor) {
            throw new Exception('Asesor no encontrado', 404);
        }

        return[
            'id' => $asesor->id,
            'nombre' => $asesor->nombre,
            'apellido' => $asesor->apellido,
            'documento' => $asesor->documento,
            'id_tipo_documento' => $asesor->id_tipo_documento,
            'fecha_nac' => $asesor->fecha_nac,
            'imagen_perfil' => $asesor->imagen_perfil,
            'direccion' => $asesor->direccion,
            'celular' => $asesor->celular,
            'genero' => $asesor->genero,
            'id_municipio' => $asesor->id_municipio,
            'id_departamento' => $asesor->id_departamento,
            'municipio_nombre' => $asesor->municipio_nombre,
            'departamento_nombre' => $asesor->departamento_nombre,
            'email' => $asesor->email,
            'estado' => $asesor->estado == 1 ? 'Activo' : 'Inactivo',
        ];
    }

    public function updateAsesorxAliado($data, $id)
    {
        $asesor = $this->asesorRepository->buscarAsesorPorId($id);

        if (!$asesor) {
            throw new Exception('Asesor no encontrado', 404);
        }

        $asesor->nombre = $data['nombre'];
        $asesor->apellido = $data['apellido'];
        $newCelular = $data['celular'];
        $asesor->documento = $data['documento'];
        $asesor->direccion = $data['direccion'];
        $asesor->genero = $data['genero'];
        $asesor->fecha_nac = $data['fecha_nac'];
        $asesor->id_tipo_documento = $data['id_tipo_documento'];
        $asesor->id_departamento = $data['id_departamento'];
        $asesor->id_municipio = $data['id_municipio'];

        if ($newCelular && $newCelular !== $asesor->celular) {
            $existing = $this->asesorRepository->findByCelular($newCelular);
            if ($existing) {
                throw new Exception('El numero de celular ya ha sido registrado anteriormente', 400);
            }
            $asesor->celular = $newCelular;
        }

        if (isset($data['imagen_perfil']) && $data['imagen_perfil']->isValid()) {
            Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));
            $path = $data['imagen_perfil']->store('public/fotoPerfil');
            $asesor->imagen_perfil = str_replace('public', 'storage', $path);
        }

        $this->asesorRepository->updateAsesorAliado($asesor);

        if ($asesor->auth) {
            $user = $asesor->auth;
            if (isset($data['password'])) {
                if (strlen($data['password']) < 8) {
                    throw new Exception('La contraseña debe tener al menos 8 caracteres', 400);
                }
                $user->password = Hash::make($data['password']);
            }

            $newEmail = $data['email'];
            if ($newEmail && $newEmail !== $user->email) {
                $existingUser = User::where('email', $newEmail)->first();
                if ($existingUser) {
                    throw new Exception('El correo electrónico ya ha sido registrado anteriormente', 400);
                }
                $user->email = $newEmail;
            }

            $user->estado = $data['estado'];
            $user->save();
        }

        return 'Asesor actualizado correctamente';
    }
}

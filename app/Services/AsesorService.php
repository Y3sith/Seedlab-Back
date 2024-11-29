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
    //Inyección de repository
    protected $asesorRepository;

    public function __construct(AsesorRepositoryInterface $asesorRepository)
    {
        $this->asesorRepository = $asesorRepository;
    }

    /**
     * Crear un nuevo asesor en la base de datos.
     * 
     * @param array $data - Los datos del asesor a crear.
     * @param UploadedFile|null $imagenPerfil - La imagen de perfil del asesor.
     * @return string - Un mensaje indicando el éxito de la operación.
     */
    public function crearAsesor(array $data, $imagenPerfil)
    {
        // Procesar la imagen de perfil y la almacena si es válida.
        $data['imagen_perfil'] = null;
        if ($imagenPerfil && $imagenPerfil->isValid()) {
            $data['imagen_perfil'] = Storage::url($imagenPerfil->store('fotoPerfil', 'public'));
        }

        // Guarda la contraseña sin hashear para enviar por correo.
        $originalPassword = $data['password'];

        // Hashear la contraseña
        $data['password'] = Hash::make($data['password']);

        // Define los parámetros para llamar al procedimiento almacenado en el repositorio.
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

        // Llama al repositorio para crear el asesor.
        $results = $this->asesorRepository->crearAsesor($params);

        // Enviar una notificación por correo al asesor recién creado con su contraseña original.
        if (!empty($results) && isset($results[0]->email)) {
            Mail::to($results[0]->email)->send(new NotificacionCrearUsuario($results[0]->email, 'Asesor', $originalPassword));
        }

        return $results[0]->mensaje;
    }


    /**
     * Actualizar los datos de un asesor.
     * 
     * @param int $id - El ID del asesor a actualizar.
     * @param array $data - Los datos a actualizar.
     * @param UploadedFile|null $imagenPerfil - La nueva imagen de perfil (si se proporciona).
     * @return Asesor - El asesor actualizado.
     * @throws Exception - Si el asesor no es encontrado.
     */
    public function actualizarAsesor($id, array $data, $imagenPerfil = null)
    {
        $asesor = $this->asesorRepository->buscarAsesorPorId($id);

        if (!$asesor) {
            throw new Exception('Asesor no encontrado');
        }

        // Si se proporciona una nueva imagen de perfil, la almacena y elimina la anterior.
        if ($imagenPerfil && $imagenPerfil->isValid()) {
            Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));
            $data['imagen_perfil'] = Storage::url($imagenPerfil->store('fotoPerfil', 'public'));
        }

        return $this->asesorRepository->actualizarAsesor($asesor, $data);
    }

    /**
     * Obtener las asesorías de un asesor por su ID.
     * 
     * @param int $id - El ID del asesor.
     * @param string $conHorario - Filtro para asesorías con o sin horario.
     * @return \Illuminate\Database\Eloquent\Collection - Las asesorías encontradas.
     */
    public function obtenerAsesoriasPorId($id, $conHorario)
    {
        // Obtiene las asesorías relacionadas con el asesor.
        $asesorias = $this->asesorRepository->buscarAsesoriasPorId($id);

        // Filtra las asesorías según si tienen o no horarios.
        return $asesorias->filter(function ($asesoria) use ($conHorario) {
            return ($conHorario === 'true' && $asesoria->horarios->isNotEmpty()) || ($conHorario === 'false' && $asesoria->horarios->isEmpty());
        });
    }

    /**
     * Obtener un asesor por su ID junto con la información de ubicación.
     * 
     * @param int $id - El ID del asesor.
     * @return array - Los datos del asesor con ubicación.
     * @throws Exception - Si el asesor no es encontrado.
     */
    public function obtenerAsesorConUbicacion($id)
    {
        $asesor = $this->asesorRepository->buscarAsesorConUbicacion($id);

        if (!$asesor) {
            throw new Exception('Asesor no encontrado', 404);
        }

        // Retorna los datos del asesor con su ubicación.
        return [
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

    /**
     * Actualizar un asesor desde un aliado.
     * 
     * @param array $data - Los datos del asesor a actualizar.
     * @param int $id - El ID del asesor a actualizar.
     * @return string - Un mensaje de éxito.
     * @throws Exception - Si el asesor o su número de celular o correo ya existen.
     */
    public function updateAsesorxAliado($data, $id)
    {
        $asesor = $this->asesorRepository->buscarAsesorPorId($id);

        if (!$asesor) {
            throw new Exception('Asesor no encontrado', 404);
        }

        // Actualiza los datos básicos del asesor.
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

        // Verifica si el nuevo número de celular ya está registrado.
        if ($newCelular && $newCelular !== $asesor->celular) {
            $existing = $this->asesorRepository->findByCelular($newCelular);
            if ($existing) {
                throw new Exception('El numero de celular ya ha sido registrado anteriormente', 400);
            }
            $asesor->celular = $newCelular;
        }

        // Actualiza la imagen de perfil si se proporciona una nueva.
        if (isset($data['imagen_perfil']) && $data['imagen_perfil']->isValid()) {
            Storage::delete(str_replace('storage', 'public', $asesor->imagen_perfil));
            $path = $data['imagen_perfil']->store('public/fotoPerfil');
            $asesor->imagen_perfil = str_replace('public', 'storage', $path);
        }

        $this->asesorRepository->updateAsesorAliado($asesor);

        // Si el asesor tiene una cuenta de usuario (auth), actualiza también su correo y contraseña.
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
            // Actualiza el estado del usuario.
            $user->estado = $data['estado'];
            $user->save();
        }

        return 'Asesor actualizado correctamente';
    }
}

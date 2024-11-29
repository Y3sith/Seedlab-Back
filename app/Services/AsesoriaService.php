<?php

namespace App\Services;

use App\Jobs\EnviarNotificacionAsesoria;
use App\Repositories\Asesorias\AsesoriaRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class AsesoriaService
{
    protected $asesoriaRepository;

    public function __construct(AsesoriaRepositoryInterface $asesoriaRepository)
    {
        $this->asesoriaRepository = $asesoriaRepository;
    }

    /**
     * Gestiona una asesoría Aliado(rechazar).
     *
     * @param int $id_asesoria
     * @param string $accion
     * @return array
     * @throws Exception
     */
    public function gestionarAsesoria(int $id_asesoria, string $accion): array
    {
        // Verifica si el usuario tiene permisos
        $userRol = Auth::user()->id_rol;
        if (!in_array($userRol, [1, 3])) {
            throw new Exception('No tienes permisos para realizar esta acción', 401);
        }

        // Gestiona la asesoría
        $mensaje = $this->asesoriaRepository->gestionarAsesoria($id_asesoria, $accion);

        return [
            'message' => $mensaje
        ];
    }

     /**
     * Crear una asesoría para un emprendedor.
     * 
     * @param array $data - Datos de la asesoría proporcionados por el emprendedor.
     * @return string - Mensaje de éxito o error.
     * @throws Exception - Lanza excepción si no se encuentra al emprendedor o si falla la asignación.
     */
    public function guardarAsesoria(array $data)
    {
        // Busca al emprendedor
        $emprendedor = $this->asesoriaRepository->encontrarEmprendedor($data['doc_emprendedor']);
        if (!$emprendedor) {
            throw new Exception('Emprendedor no encontrado');
        }

        // Lógica de asignación de orientador o aliado
        $isOrientador = $data['isorientador'] == 1;
        $destinatario = null;

        if ($isOrientador) {
            // Orientador
            $destinatario = $this->siguienteOrientador();
            if (!$destinatario) {
                throw new Exception('No hay orientadores activos disponibles');
            }
        } else {
            // Aliado
            $destinatario = $this->asesoriaRepository->encontrarAliadoPorNombre($data['nom_aliado']);
            if (!$destinatario) {
                throw new Exception('No se encontró ningún aliado con el nombre proporcionado');
            }
        }

        // Preparar los datos para crear la asesoría
        $asesoriaData = [
            'Nombre_sol' => $data['nombre'],
            'notas' => $data['notas'],
            'isorientador' => $isOrientador,
            'asignacion' => $data['asignacion'],
            'fecha' => $data['fecha'],
            'id_aliado' => $isOrientador ? null : $destinatario->id,
            'id_orientador' => $isOrientador ? $destinatario->id : null,
            'doc_emprendedor' => $data['doc_emprendedor'],
        ];

        // Guardar la asesoría
        $asesoria = $this->asesoriaRepository->crearAsesoriaEmprendedor($asesoriaData);

        // Cargar el modelo del destinatario
        $destinatario->load('auth');

        // Verificar el email y enviar el job de notificación
        if ($destinatario->auth && $destinatario->auth->email) {
            $notificacionData = [
                'asesoria' => $asesoria,
                'destinatario' => $destinatario,
                'emprendedor' => $emprendedor,
                'isOrientador' => $isOrientador,
            ];

            // Dispara el job de la notificación de asesoría
            EnviarNotificacionAsesoria::dispatch('notificacion_asesoria_general', $notificacionData);
        }

        return 'La asesoría se ha solicitado con éxito';
    }


    /**
     * Asignar un asesor a una asesoría para un aliado.
     * 
     * @param array $data - Datos de la asesoría y del asesor.
     * @return string - Mensaje de éxito o error.
     * @throws Exception - Lanza excepción si ya está asignada o si ocurre un error.
     */
    public function asignarAsesoria($data)
    {
        // Verificar si ya está asignada
        $asesoriaAsignada = $this->asesoriaRepository->verificarAsesoriaAsignada($data['id_asesoria']);
        if ($asesoriaAsignada) {
            throw new Exception('Esta asesoría ya se ha asignado, edita la asignación');
        }

        // Asignar el asesor a la asesoría
        $nuevaAsignacion = $this->asesoriaRepository->asignarAsesor($data);

        // Actualizar el estado de la asesoría para reflejar la asignación
        $this->asesoriaRepository->actualizarEstadoAsesoria($data['id_asesoria'], 1);

        // Obtener los detalles del asesor, la asesoría, y el emprendedor involucrado
        $asesor = $this->asesoriaRepository->obtenerAsesorPorId($data['id_asesor']);
        $asesoria = $this->asesoriaRepository->obtenerAsesoriaPorId($data['id_asesoria']);
        $emprendedor = $this->asesoriaRepository->encontrarEmprendedor($asesoria->doc_emprendedor);

        // Preparar los datos para el Job
        $notificacionData = [
            'asesoria' => $asesoria,
            'destinatario' => $asesor,
            'emprendedor' => $emprendedor,
            'nombreAsesor' => $asesor->nombre . ' ' . $asesor->apellido,
            'nombreEmprendedor' => $emprendedor->nombre,
        ];

        // Dispara el Job unificado para enviar la notificación de asesoría asignada
        EnviarNotificacionAsesoria::dispatch('notificacion_asesoria_asesor', $notificacionData);

        return 'Se ha asignado correctamente el asesor para esta asesoría';
    }


    /**
     * Definir un horario para la asesoría.
     * 
     * @param array $data - Datos del horario y la asesoría.
     * @return string - Mensaje de éxito o error.
     * @throws Exception - Lanza excepción si ya existe un horario o si hay problemas con los datos.
     */
    public function definirHorarioAsesoria(array $data)
    {
        // Verificar si la asesoría existe
        $asesoria = $this->asesoriaRepository->obtenerAsesoriaPorId($data['id_asesoria']);
        if (!$asesoria) {
            throw new Exception('La asesoría no existe');
        }

        // Verificar si el emprendedor existe
        $emprendedor = $this->asesoriaRepository->encontrarEmprendedor($asesoria->doc_emprendedor);
        if (!$emprendedor) {
            throw new Exception('El emprendedor no existe');
        }

        // Verificar si el asesor asignado a la asesoría existe
        $asesor = $this->asesoriaRepository->obtenerAsesorPorAsesoriaId($data['id_asesoria']);
        if (!$asesor) {
            throw new Exception('No se encontró ningún asesor asignado a esta asesoría');
        }

        // Verificar si ya hay un horario asignado para esta asesoría
        if ($this->asesoriaRepository->verificarHorarioExistente($data['id_asesoria'])) {
            throw new Exception('La asesoría ya tiene una fecha asignada');
        }

        // Crear el nuevo horario de la asesoría
        $horarioAsesoria = $this->asesoriaRepository->crearHorarioAsesoria([
            'observaciones' => $data['observaciones'] ?? 'Ninguna observación',
            'fecha' => $data['fecha'],
            'estado' => 'Pendiente',
            'id_asesoria' => $data['id_asesoria'],
        ]);

        // Preparar datos para el Job unificado
        $notificacionData = [
            'destinatario' => $emprendedor,
            'asesoria' => $asesoria,
            'asesor' => $asesor,
            'horarioAsesoria' => $horarioAsesoria,
        ];

        // Despachar el Job para enviar el correo de notificación al emprendedor
        EnviarNotificacionAsesoria::dispatch('notificacion_asesoria_emprendedor', $notificacionData);

        return 'Se le ha asignado un horario a su asesoría';
    }

    /**
     * Obtener asesorías por emprendedor.
     * 
     * @param string $documento - Documento del emprendedor.
     * @param string $asignacion - Estado de la asignación (ej. "pendiente").
     * @return Collection - Colección de asesorías del emprendedor.
     */
    public function obtenerAsesoriasPorEmprendedor($documento, $asignacion)
    {
        return $this->asesoriaRepository->obtenerAsesoriasPorEmprendedor($documento, $asignacion);
    }

    /**
     * Obtener asesorías por aliado.
     * 
     * @param int $aliadoId - ID del aliado.
     * @param string $asignacion - Estado de la asignación.
     * @return Collection - Colección de asesorías del aliado.
     */
    public function obtenerAsesoriasPorAliado($aliadoId, $asignacion)
    {
        return $this->asesoriaRepository->obtenerAsesoriasPorAliado($aliadoId, $asignacion);
    }


    /**
     * Listar asesores disponibles para un aliado.
     * 
     * @param int $idAliado - ID del aliado.
     * @return Collection - Colección de asesores disponibles.
     */
    public function listarAsesoresDisponibles($idAliado)
    {
        return $this->asesoriaRepository->obtenerAsesoresDisponiblesPorAliado($idAliado);
    }

    /**
     * Obtener el siguiente orientador disponible para una asesoría.
     * 
     * @return Orientador|null - Devuelve el próximo orientador disponible o null si no hay.
     */
    public function siguienteOrientador()
    {
        // Obtiene los orientadores activos.
        $orientadoresActivos = $this->asesoriaRepository->obtenerOrientadoresActivos();

        if ($orientadoresActivos->isEmpty()) {
            return null;
        }

        // Obtiene la última asesoría asignada a un orientador.
        $ultimaAsesoria = $this->asesoriaRepository->obtenerUltimaAsesoriaConOrientador();

        // Si no hay última asesoría, devuelve el primer orientador.
        if (!$ultimaAsesoria) {
            return $orientadoresActivos->first();
        }

        // Busca el índice del último orientador asignado.
        $ultimoIndex = $orientadoresActivos->search(function ($orientador) use ($ultimaAsesoria) {
            return $orientador->id == $ultimaAsesoria->id_orientador;
        });

        if ($ultimoIndex === false) {
            return $orientadoresActivos->first();
        }

        // Calcula el índice del próximo orientador disponible.
        $proximoIndex = ($ultimoIndex + 1) % $orientadoresActivos->count();
        return $orientadoresActivos[$proximoIndex];
    }
}

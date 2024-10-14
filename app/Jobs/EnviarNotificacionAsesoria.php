<?php

namespace App\Jobs;

use App\Mail\NotificacionAsesoria;
use App\Mail\NotificacionAsesoriaAsesor;
use App\Mail\NotificacionesAsesoriaAliado;
use App\Mail\NotificacionAsesoriaEmprendedor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionAsesoria implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tipoNotificacion;
    protected $datos;

    /**
     * Crea una nueva instancia del job.
     */
    public function __construct(string $tipoNotificacion, array $datos)
    {
        $this->tipoNotificacion = $tipoNotificacion;
        $this->datos = $datos;
    }

    /**
     * Ejecuta el Job.
     */
    public function handle()
    {
        switch ($this->tipoNotificacion) {
            case 'notificacion_asesoria_asesor':
                Mail::to($this->datos['destinatario']->auth->email)
                    ->send(new NotificacionAsesoriaAsesor(
                        $this->datos['destinatario'],
                        $this->datos['asesoria'],
                        $this->datos['nombreAsesor'],
                        $this->datos['nombreEmprendedor']
                    ));
                break;

            case 'notificacion_asesoria_aliado':
                Mail::to($this->datos['destinatario']->auth->email)
                    ->send(new NotificacionesAsesoriaAliado(
                        $this->datos['destinatario'],
                        $this->datos['asesoria'],
                        $this->datos['emprendedor']
                    ));
                break;

            case 'notificacion_asesoria_emprendedor':
                Mail::to($this->datos['destinatario']->auth->email)
                    ->send(new NotificacionAsesoriaEmprendedor(
                        $this->datos['destinatario'],
                        $this->datos['asesoria'],
                        $this->datos['asesor'],
                        $this->datos['emprendedor'],
                        $this->datos['horarioAsesoria']
                    ));
                break;

            case 'notificacion_asesoria_general':
                Mail::to($this->datos['destinatario']->auth->email)
                    ->send(new NotificacionAsesoria(
                        $this->datos['asesoria'],
                        $this->datos['destinatario'],
                        $this->datos['emprendedor'],
                        $this->datos['isOrientador']
                    ));
                break;

            default:
                // Manejo de casos no esperados
                throw new \Exception("Tipo de notificación no soportado: {$this->tipoNotificacion}");
        }
    }
}

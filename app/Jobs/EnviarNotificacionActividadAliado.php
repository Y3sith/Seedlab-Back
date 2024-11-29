<?php

namespace App\Jobs;

use App\Mail\NotificacionActividadAliado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionActividadAliado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nombreActividad;
    protected $destinatario;

    /**
     * Create a new job instance.
     */
    public function __construct($nombreActividad, $destinatario)
    {
        $this->nombreActividad = $nombreActividad;
        $this->destinatario = $destinatario;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->destinatario->auth->email)
            ->send(new NotificacionActividadAliado($this->nombreActividad, $this->destinatario->nombre));
    }
}

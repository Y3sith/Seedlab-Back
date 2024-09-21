<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionActividadAliado extends Mailable
{
    use Queueable, SerializesModels;

    // public $actividad;
    public $destinatario;
    
    public function __construct($destinatario)
    {
        // $this->actividad = $actividad;
        $this->destinatario = $destinatario;
    }

    public function build()
    {

    return $this
        ->subject("Nueva Actividad Asignada")
        ->view('notificacion-actividad-aliado');

}
}

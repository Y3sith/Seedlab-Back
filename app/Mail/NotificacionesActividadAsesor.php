<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionesActividadAsesor extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreActividad;
    public $nombreniveles;
    public $destinatario;

    public function __construct($nombreActividad, $nombreniveles, $destinatario)
    {
        $this->nombreActividad = $nombreActividad;
        $this->nombreniveles = $nombreniveles;
        $this->destinatario = $destinatario;
    }

    public function build()
    {

    return $this
        ->subject("Nuevo Nivel Asignado")
        ->view('notificacion-actividad-asesor')
        ->with([
            'nombreActividad' => $this->nombreActividad,
            'nombreniveles' => $this->nombreniveles,
            'destinatario' => $this->destinatario,
        ]);
}
    
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionesAsesoriaAliado extends Mailable
{
    use Queueable, SerializesModels;
    public $destinatario;
    public $asesoria;
    public $emprendedor;
    public function __construct($destinatario, $asesoria, $emprendedor)
    {
        $this->destinatario = $destinatario;
        $this->asesoria = $asesoria;
        $this->emprendedor = $emprendedor;
        
    }

    public function build()
    {

    return $this
        ->subject("Nueva Asesoría Asignada para Aliado")
        ->view('notificacion-asesora-aliado')
        ->with([
            'destinatario' => $this->destinatario,
            'asesoria' => $this->asesoria,
            'emprendedor' => $this->emprendedor

        ]);
}
   
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionAsesoriaAsesor extends Mailable
{
    use Queueable, SerializesModels;
    public $destinatario;
    public $asesoria;
    public $nombreAsesor;
    public $nombreEmprendedor;

    public function __construct($destinatario, $asesoria, $nombreAsesor, $nombreEmprendedor)
    {
        $this->destinatario = $destinatario;
        $this->asesoria = $asesoria;
        $this->nombreAsesor = $nombreAsesor;
        $this->nombreEmprendedor = $nombreEmprendedor;

    }

    /**
     * Get the message envelope.
     */
    
     public function build()
     {
        return $this
        ->subject("Nueva Asesoría Asignada")
        ->view('notificacion-asesoria-asesor')
        ->with([
            'destinatario' => $this->destinatario,
            'asesoria' => $this->asesoria,
            'nombreAsesor' => $this->nombreAsesor,
            'nombreEmprededor' => $this->nombreEmprendedor
        ]);
 }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionAsesoriaEmprendedor extends Mailable
{
    use Queueable, SerializesModels;

    
    public $destinatario;
    public $asesoria;
    public $nombreAsesor;
    public $emprendedor;
    public $horarioAsesoria;

    public function __construct($destinatario, $asesoria, $nombreAsesor, $emprendedor, $horarioAsesoria)
    {
        $this->destinatario = $destinatario;
        $this->asesoria = $asesoria;
        $this->nombreAsesor = $nombreAsesor;
        $this->emprendedor = $emprendedor;
        $this->horarioAsesoria = $horarioAsesoria;
    }

    public function build()
    {

    return $this
        ->subject("Asesoria Asignada Correctamente")
        ->view('notificacion-asesoria-emprendedor')
        ->with([
            'destinatario' => $this->destinatario,
            'asesoria' => $this->asesoria,
            'nombreAsesor' => $this->nombreAsesor,
            'emprendedor' => $this->emprendedor,
            'horarioAsesoria' => $this->horarioAsesoria,
        ]);
}

    
}

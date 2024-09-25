<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionAsesoria extends Mailable
{
    use Queueable, SerializesModels;

    public $asesoria;
    public $destinatario;
    public $emprendedor;
    public $isOrientador;

    public function __construct($asesoria, $destinatario, $emprendedor, $isOrientador)
    {
        $this->asesoria = $asesoria;
        $this->destinatario = $destinatario;
        $this->emprendedor = $emprendedor;
        $this->isOrientador = $isOrientador;
    }

    public function build()
    {
        $tipoDestinatario = $this->isOrientador ? 'Orientador' : 'Aliado';

    return $this
        ->subject("Nueva Asesoría Asignada para {$tipoDestinatario}")
        ->view('notificacion-asesoria')
        ->with([
            'tipoDestinatario' => $tipoDestinatario,
            'destinatario' => $this->destinatario,
            'asesoria' => $this->asesoria,
            'emprendedor' => $this->emprendedor,
        ]);
}
}

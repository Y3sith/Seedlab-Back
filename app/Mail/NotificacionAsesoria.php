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
        $greeting = "<html><body><h1>Nueva Asesoría Asignada para {$tipoDestinatario}</h1>";

        $content = "$greeting<p>Hola {$this->destinatario->auth->name},</p>";
        $content .= "<p>Se te ha asignado una nueva asesoría con los siguientes detalles:</p>";
        $content .= "<ul>";
        $content .= "<li>Nombre de la solicitud: {$this->asesoria->Nombre_sol}</li>";
        $content .= "<li>Fecha: {$this->asesoria->fecha}</li>";
        $content .= "<li>Emprendedor: {$this->emprendedor->nombre}</li>";
        $content .= "</ul>";
        $content .= "<p>Por favor, revisa tu agenda y prepárate para la asesoría.</p>";
        $content .= "<p>Gracias por tu colaboración.</p>";
        $content .= "</body></html>";

        return $this
        //->to($this->aliado->user->email)
        //->view('temporary-password')
        ->subject("Nueva Asesoría Asignada para {$tipoDestinatario}")
        ->html($content);
}

    /**
     * Get the message envelope.
     */
}

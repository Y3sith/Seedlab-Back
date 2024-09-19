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
    public $aliado;
    public $emprendedor;

    public function __construct($asesoria, $aliado, $emprendedor)
    {
        $this->asesoria = $asesoria;
        $this->aliado = $aliado;
        $this->emprendedor = $emprendedor;
    }

    public function build()
    {
        $greeting = '<html><body><h1>Nueva Asesoría Asignada</h1>';

        $content = "$greeting<p>Hola {$this->aliado->nombre},</p>";
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
        ->view('temporary-password')
        ->subject('Nueva Asesoría Asignada')
        ->html($content);
}

    /**
     * Get the message envelope.
     */
}

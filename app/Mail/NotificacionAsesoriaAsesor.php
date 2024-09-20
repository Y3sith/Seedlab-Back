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
    public $newasesoria;
    public $destinatario;
    public $asesoria;
    public $nombreAsesor;

    public function __construct($newasesoria, $destinatario, $asesoria, $nombreAsesor)
    {
        $this->newasesoria = $newasesoria;
        $this->destinatario = $destinatario;
        $this->asesoria = $asesoria;
        $this->nombreAsesor = $nombreAsesor;
    }

    /**
     * Get the message envelope.
     */
    
     public function build()
     {
        //  $greeting = "<html><body><h1>Nueva Asesoría Asignada para Asesor</h1>";
 
        //  $content = "$greeting<p>Hola {$this->destinatario->auth->name},</p>";
        //  $content .= "<p>Se te ha asignado una nueva asesoría con los siguientes detalles:</p>";
        //  $content .= "<ul>";
        //  $content .= "<li>Nombre de la solicitud: {$this->asesoria->Nombre_sol}</li>";
        //  $content .= "<li>Fecha: {$this->asesoria->fecha}</li>";
        //  $content .= "<li>Emprendedor: {$this->emprendedor->nombre}</li>";
        //  $content .= "</ul>";
        //  $content .= "<p>Por favor, revisa tu agenda y prepárate para la asesoría.</p>";
        //  $content .= "<p>Gracias por tu colaboración.</p>";
        //  $content .= "</body></html>";
 
        //  return $this
        //  //->to($this->aliado->user->email)
        //  //->view('temporary-password')
        //  ->subject("Nueva Asesoría Asignada para {$tipoDestinatario}")
        //  ->html($content);
 }
}

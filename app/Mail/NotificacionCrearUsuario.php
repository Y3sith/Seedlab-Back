<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionCrearUsuario extends Mailable
{
    use Queueable, SerializesModels;
    
    public $email;
    public $rol;
    public $mensajecontrasena;
    public function __construct($email, $rol, $mensajecontrasena)
    {
        $this->email = $email;
        $this->rol = $rol;
        $this->mensajecontrasena = $mensajecontrasena;
    }

    public function build()
     {
        return $this
        ->subject("Bienvenido a Seedlab")
        ->view('notificacion-nuevo-usuario')
        ->with([
            'email' => $this->email,
            'rol' => $this->rol,
            'mensajecontrasena' => $this->mensajecontrasena
        ]);
 }
}

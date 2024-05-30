<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    private $temporaryPassword;

    public function __construct($temporaryPassword)
    {
        $this->temporaryPassword = $temporaryPassword;
    }

    public function build()
    {
        $greeting = '<html> Hola <br>';

        $content = "$greeting <br>";
        $content .= "Recibes este correo electrónico porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.<br>";
        $content .= "Tu nueva contraseña temporal es: <b> $this->temporaryPassword </b><br>";
        $content .= "Por favor, utiliza esta contraseña para iniciar sesión y asegúrate de cambiarla después.<br>";
        $content .= "Si no realizaste esta solicitud, puedes ignorar este correo.</html>";

        return $this
                    ->view('temporary-password')
                   ->with(['temporaryPassword'=> $this->temporaryPassword]);
        
                    //->subject('Restablecer contraseña')
                   //  ->html($content);
    }
}
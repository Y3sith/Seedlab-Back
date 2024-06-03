<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationCode;
    private $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($verificationCode,)
    {
        $this->verificationCode = $verificationCode;
    }
    public function ___construct($code){
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $content = "<html> Hola <br>";
        $content .= "Recibes este correo electrónico porque hemos recibido una solicitud de una creación de una cuenta en Reciward.<br>";
        $content .= "Debes digitar el siguiente codigo de 6 digitos para verificar tu cuenta. <br>";
        $content .= "Codigo: <b>".$this->code."</b> <br>";
        $content .= "Si no realizaste esta solicitud, puedes ignorar este correo.</html>";
        return $this->view('verification-code') 
                    ->with(['verificationCode' => $this->verificationCode]);   
    }
}
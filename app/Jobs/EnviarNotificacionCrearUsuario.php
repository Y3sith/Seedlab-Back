<?php

namespace App\Jobs;

use App\Mail\NotificacionCrearUsuario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionCrearUsuario implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $rol;
    protected $password;

    public function __construct($email, $rol, $password)
    {
        $this->email = $email;
        $this->rol = $rol;
        $this->password = $password;
    }

    public function handle()
    {
        // Log::info('Iniciando el envío de correo', [
        //     'email' => $this->email,
        //     'rol' => $this->rol,
        //     'password' => $this->password
        // ]);

        try {
            Mail::to($this->email)->send(new NotificacionCrearUsuario($this->email, $this->rol, $this->password));
            //Log::info('Correo enviado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al enviar correo en EnviarNotificacionCrearUsuario:', [
                'message' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeEmail;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);


        //dd(!Auth::attempt($credentials));

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $request->user();


        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'user' => $user
        ]);

    }


    public function userProfile()
    {
        return response()->json(["clave" => "Hola"]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }


    protected function register(Request $data)
    {
        $response = null;
        $statusCode = 200;

        $verificationCode = mt_rand(10000, 99999);

        DB::transaction(function () use ($data, $verificationCode, &$response, &$statusCode) {
            $results = DB::select('CALL sp_registrar_emprendedor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)', [
                $data['documento'],
                $data['nombretipodoc'],
                $data['nombre'],
                $data['apellido'],
                $data['celular'],
                $data['genero'],
                $data['fecha_nacimiento'],
                $data['municipio'],
                $data['direccion'],
                $data['email'],
                Hash::make($data['password']),
                $data['estado'],
                $verificationCode
            ]);

            if (!empty ($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El numero de documento ya ha sido registrado en el sistema' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                    $statusCode = 400;
                }
                else{
                    Mail::to($data['email'])->send(new VerificationCodeEmail($verificationCode));
                    //el codigo de abajo ejecuta el job (aun no se ha definido si se usara ya que se necesita el comando "php artisan queue:work")
                    //dispatch(new SendVerificationEmail($data['email'], $verificationCode));
                }
            }
        });

        
        return response()->json(['message' => $response], $statusCode);
    }

    protected function validate_email(Request $request)
    {
        $response = null;
        $statusCode = 200;

        DB::transaction(function () use ($request, &$response, &$statusCode) {
            $results = DB::select('CALL sp_validar_correo(?,?)', [
                $request['email'],
                $request['codigo'],
            ]);

            if (!empty ($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El correo electrónico no esta registrado' || $response === 'El código de verificación proporcionado no coincide') {
                    $statusCode = 400;
                } elseif ($response === 'El correo electrónico ya ha sido verificado anteriormente') {
                    $statusCode = 409;
                }
            }
        });
        return response()->json(['message' => $response], $statusCode);
    }

    public function allUsers()
    {
    }
}

// JSON DE EJEMPLO PARA LOS ENDPOINT

// register:
//  {
//     "documento": "1000",
//     "nombretipodoc": "Cédula de Ciudadanía",
//     "nombre": "Juancamilo",
//     "apellido": "DavidHernandez",
//     "celular": "31465994442",
//     "genero": "Masculino",
//     "fecha_nacimiento": "1990-01-01",
//     "municipio": "Argelia",
//     "direccion": "cra 34 34-12",
//     "email": "brayanfigueroajerez@gmail.com",
//     "contrasena": "1",
//     "estado": true
// }

// validate_email:
// {
//     "email": "brayanfigueroajerez@gmail.com",
//     "codigo": "69838"
// }

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeEmail;
use App\Models\Emprendedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required','string'],
        ]);

        
        dd(!Auth::attempt($credentials));

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
            'message' => 'Successfully logged out'
        ]);
    }


    protected function existeusuario(string $documento)
    {
        $valuser = Emprendedor::where('documento', $documento)->first();

        if ($valuser) {
            return 'Tu documento ya existe en el sistema';
        } else {
            return null;
        }
    }

    protected function register(Request $data)
    {
        // Verificar si el usuario ya existe
        $Response = $this->existeusuario($data['documento']);

        if ($Response != null) {
            return response()->json(['message' => $Response], 400);
        } else {
            // Generar código de verificación
            $verificationCode = mt_rand(10000, 99999);

            // Ejecutar procedimiento almacenado
            DB::transaction(function () use ($data, $verificationCode) {
                // Ejecutar el procedimiento almacenado para registrar el emprendedor
                DB::select('CALL sp_registrar_emprendedor(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)', [
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

                // Enviar correo electrónico con el código de verificación
                Mail::to($data['email'])->send(new VerificationCodeEmail($verificationCode));
            });

            // Retornar respuesta exitosa
            return response()->json(['message' => 'Tu usuario ha sido creado con éxito'], 201);
        }
    }






    protected function validate_email(Request $request)
    {
        $verificationCode = $request->input('codigo');

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            if ($user->email_verified_at != null) {
                return response()->json(['message' => 'Tu correo electrónico ya ha sido validado'], 400);
            } else {
                if ($user->verification_code === $verificationCode) {
                    $user->email_verified_at = now();
                    $user->save();

                    return response()->json(['message' => 'Correo electrónico validado correctamente'], 200);
                } else {
                    return response()->json(['message' => 'Tu codigo de verificación es incorrecto'], 400);
                }
            }
        } else {
            return response()->json(['message' => 'Tu correo no esta registrado en el sistema'], 400);
        }
    }



    public function allUsers()
    {
    }
}


// JSON DE EJEMPLO PARA LOS ENDPOINT


// register:
// {
//     "numdocumento": "123",
//     "nombre": "Brayan Esneider",
//     "apellido": "Figueroa Jerez",
//     "celular": "3146599453",
//     "genero": "Masculino",
//     "email": "brayanfigueroajerez@gmail.com",
//     "fecha_nacimiento": "2005-04-07",
//     "id_departamento": 1,
//     "id_municipio": 1,
//     "password": "1234",
//     "id_estado": 1,
//     "id_tipo_documento": 1,
//     "id_roles": 1 
// }

// validate_email:
// {
//     "email": "brayanfigueroajerez@gmail.com",
//     "codigo": "69838"
// }

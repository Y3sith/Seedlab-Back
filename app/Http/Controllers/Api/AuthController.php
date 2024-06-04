<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\PasswordReset;
use App\Mail\VerificationCodeEmail;
use App\Models\Emprendedor;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);


        $user = User::where('email', $request->email)->with('emprendedor')->first();
        $verificationCode = mt_rand(10000, 99999);

        //Si el usuario no existe, validacion de credenciales 
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'No se encuentra registrado'], 401);
        }


        //Que el campo de verificacion de email del rol emprendedor no sea nullo
        if ($user->id_rol == 5 && !$user->emprendedor->email_verified_at) {
            $user->emprendedor->cod_ver = $verificationCode;
            $user->emprendedor->save();
            Mail::to($user['email'])->send(new VerificationCodeEmail($verificationCode));
            return response()->json(['message' => 'Por favor verifique su correo electronico'], 403);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();
        $additionalInfo = $this->getAdditionalInfo($user);
        $info = [];
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            //'user' => $user,
            'user' => $additionalInfo
        ]);
    }

    protected function getAdditionalInfo($user)
    {
        $info = [];

        if ($user->id_rol == 3) {
            $info = [

                'id' => $user->aliado->id,
                'nombre' => $user->aliado->nombre,
                'id_autentication' => $user->aliado->id_autentication,
                'id_rol' => $user->id_rol
            ];
        } elseif ($user->id_rol == 4) {
            $info = [

                'id' => $user->asesor->id,
                'id_autentication' => $user->asesor->id_autentication,
                'id_aliado' => $user->asesor->id_aliado,
                'id_rol' => $user->id_rol
            ];
        } elseif ($user->id_rol == 5) {
            $info = $user;
        } elseif ($user->id_rol == 1) {
            $info = [
                'id' => $user->superadmin->id,
                'nombre' => $user->superadmin->nombre,
                'apellido' => $user->superadmin->apellido,
                'id_autentication' => $user->superadmin->id_autentication,
                'id_rol' => $user->id_rol
            ];
        } elseif ($user->id_rol == 2) {
            $info = [
                //$user,
                'id' => $user->orientador->id,
                'nombre' => $user->orientador->nombre,
                'apellido' => $user->orientador->apellido,
                'id_autentication' => $user->orientador->id_autentication,
                'id_rol' => $user->id_rol
            ];
        }


        return $info;
    }


    public function userProfile($documento)
    {
        if (Auth::user()->id_rol !== 5) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $emprendedor = Emprendedor::where('documento', $documento)
            ->with('auth:id,email')
            ->select('nombre', 'apellido', 'documento', 'celular', 'genero', 'fecha_nac', 'direccion', 'id_municipio', 'id_autentication', 'id_tipo_documento')
            ->first();
        return response()->json($emprendedor);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    protected function existeusuario($documento)
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
        $response = null;
        $statusCode = 200;

        $verificationCode = mt_rand(10000, 99999);

        if (strlen($data['password']) < 8) {
            $statusCode = 400;
            $response = 'La contraseña debe tener al menos 8 caracteres';
            return response()->json(['message' => $response], $statusCode);
        }

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

            if (!empty($results)) {
                $response = $results[0]->mensaje;
                if ($response === 'El numero de documento ya ha sido registrado en el sistema' || $response === 'El correo electrónico ya ha sido registrado anteriormente') {
                    $statusCode = 400;
                } else {
                    Mail::to($data['email'])->send(new VerificationCodeEmail($verificationCode));
                    //el codigo de abajo ejecuta el job (aun no se ha definido si se usara ya que se necesita el comando "php artisan queue:work")
                    //dispatch(new SendVerificationEmail($data['email'], $verificationCode));
                }
            }
        });


        return response()->json(['message' => $response, 'email' => $data->email],  $statusCode);
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

            if (!empty($results)) {
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
        $users = User::with('emprendedor')->get();
        return response()->json($users);
    }


    /*Metodo que maneja el envio del correo para restablecer la contraseña
    */
    public function enviarRecuperarContrasena(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        if (!$email) {
            return response()->json(['error' => 'Proporciona un email válido'], 400);
        }
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Cuenta no existe'], 400);
        }

        // Generar una contraseña temporal aleatoria
        $temporaryPassword = Str::random(10); // Usar 10 caracteres para la contraseña temporal

        // Actualizar la contraseña del usuario en la base de datos
        $user->password = Hash::make($temporaryPassword);
        $user->save();

        // Enviar la contraseña temporal por correo electrónico
        Mail::to($email)->send(new PasswordReset($temporaryPassword));
        

        return response()->json(['message' => 'Te hemos enviado un email con tu nueva contraseña temporal'], 200);
    }

    public function sendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Existing email'], 400);
        }

        $code = rand(100000, 999999);
        Mail::to($request->email)->send(new VerificationCodeEmail($code));

        return response()->json([
            'message' => 'Mail sent',
            'code' => $code
        ], 200);
    }


    /*public function resetPassword(Request $request)
    {
        $request->merge(['password' => trim($request->password)]);

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'min:8', 'regex:/^\S*(\s\S*)?$/'],
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'La contraseña debe tener al menos 8 caracteres y no puede contener espacios en medio'], 400);
        }

        try {
            $user = User::where('reset_token', $request->token)->first();

            if (!$user) {
                return response()->json(['error' => 'Token inválido o expirado'], 400);
            }

            $tokenCreationTime = new Carbon($user->token_created_at);
            if ($tokenCreationTime->addHour() < now()) {
                return response()->json(['error' => 'Token expirado'], 400);
            }
            $user->password = Hash::make($request->password);

            if ($user->emprendedor) {
                $emprendedor = $user->emprendedor;
                $user->password;
                $emprendedor->save();
            }

            $user->reset_token = null;
            $user->token_created_at = null;
            $user->save();

            return response()->json(['message' => 'Contraseña restablecida correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al restablecer la contraseña: ' . $e->getMessage()], 400);
        }
    }*/
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

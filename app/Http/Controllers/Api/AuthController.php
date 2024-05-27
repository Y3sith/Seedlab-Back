<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeEmail;
use App\Models\Emprendedor;
use App\Models\User;
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

        $user = User::where('email', $request->email)->with('emprendedor')->first();
        $verificationCode = mt_rand(10000, 99999);

        //Si el usuario no existe, validacion de credenciales 
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
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
            'additional_info' => $additionalInfo
        ]);
    }

    protected function getAdditionalInfo($user)
    {
        $info = [];

        if ($user->id_rol == 3) {
            $info = [
                'id'=>$user->aliado->id,
                'nombre' => $user->aliado->nombre,
                'id_autentication' => $user->aliado->id_autentication,
                'id_rol' => $user->id_rol
                
            ];
        } elseif ($user->id_rol == 4) {
            $info = [
                'id'=>$user->asesor->id,
                'id_autentication' => $user->asesor->id_autentication,
                'id_aliado' => $user->asesor->id_aliado,
                'id_rol' => $user->id_rol
                
            ];
        } elseif ($user->id_rol == 5){
            $info = [
                'nombre' => $user->emprendedor->nombre,
                'apellido' => $user->emprendedor->apellido,
                'documento' => $user->emprendedor->documento,
                'celular' => $user->emprendedor->celular,
                'genero' => $user->emprendedor->genero,
                'fecha_nac' => $user->emprendedor->fecha_nac,
                'direccion' => $user->emprendedor->direccion,
                'id_autentication' => $user->emprendedor->id_autentication,
                'id_tipo_documento' => $user->emprendedor->id_tipo_documento,
                'id_municipio' => $user->emprendedor->id_municipio,
                'id_rol' => $user->id_rol
                
            ];
        } elseif ($user->id_rol == 1){
            $info = [
                'id'=>$user->superadmin->id,
                'nombre'=>$user->superadmin->nombre,
                'apellido' => $user->superadmin->apellido,
                'id_autentication' => $user->superadmin->id_autentication,
                'id_rol' => $user->id_rol
                
                
            ];
        } elseif ($user->id_rol == 2){
            $info = [
                'id'=>$user->organizador->id,
                'nombre'=>$user->organizador->nombre,
                'apellido' => $user->organizador->apellido,
                'id_autentication' => $user->organizador->id_autentication,
                'id_rol' => $user->id_rol
            ];
        }
    

        return $info;
    }


    public function userProfile($documento)
    {
        $emprendedor = Emprendedor::where('documento', $documento)
            ->with('auth:id,email,password') 
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

        if(strlen($data['password']) <8) {
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

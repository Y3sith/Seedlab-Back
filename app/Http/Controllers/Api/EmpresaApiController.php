<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApoyoEmpresa;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class EmpresaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        /*muestras las empresas*/
        if (Auth::user()->id_rol != 1) {
            return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
        }
        $empresa = Empresa::paginate(5);
        return new JsonResponse($empresa->items());
    }

    public function getOnlyempresa($id_emprendedor, $documento)
{
    /* Muestra la empresa específica del emprendedor */
    if (Auth::user()->id_rol != 5) {
        return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
    }

    // Encuentra la empresa específica del emprendedor con el id proporcionado
    $empresa = Empresa::where('id_emprendedor', $id_emprendedor)
                      ->where('documento', $documento) // Asumiendo que 'id' es el campo que identifica a la empresa
                      ->first();

    if (!$empresa) {
       return response()->json(["error" => "Empresa no encontrada"], 404);
    }

    $apoyo = ApoyoEmpresa::where('id_empresa', $empresa->documento)->first();

    // Convierte la empresa en un array
    $data = $empresa->toArray();

    // Si hay un apoyo, lo agrega al array
    if ($apoyo) {
        $data['apoyo'] = $apoyo;
    }

    return response()->json($data, 200);
    }

    public function getApoyosxEmpresa($id_empresa)
    {
        try {

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            $apoyos = ApoyoEmpresa::all()->where('id_empresa', $id_empresa);
            return response()->json($apoyos, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function getApoyoxDocumento($documento)
    {
        try {

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            $apoyos = ApoyoEmpresa::all()->where('documento', $documento)->first();
            return response()->json($apoyos, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function crearApoyos(Request $request)
    {
        try{

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }

            $apoyo = ApoyoEmpresa::create([
                'documento' => $request->documento,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cargo' => $request->cargo,
                'telefono' => $request->telefono,
                'celular' => $request->celular,
                'email' => $request->email,
                'id_tipo_documento' => $request->id_tipo_documento,
                'id_empresa' => $request->id_empresa,

            ]);
            return response()->json(['message' => 'Apoyo creado con exito'], 201);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
    
    public function editarApoyo (Request $request, $documento)
    {
        try{

            if (Auth::user()->id_rol != 5) {
                return response()->json(['error' => 'no tienes permiso para acceder']);
            }
            
            $apoyo = ApoyoEmpresa::where('documento', $documento);
            
            if (!$apoyo) {
            return response()->json(['error' => 'Apoyo no encontrado'], 404);
            }

            $apoyo->update([
                'documento' => $request->input('documento'),
                'nombre' => $request->input('nombre'),
                'cargo' => $request->input('cargo'),
                'telefono' => $request->input('telefono'),
                'celular' => $request->input('celular'),
                'email' => $request->input('email'),
                'id_tipo_documento' => $request->input('id_tipo_documento'),
            ]);

            return response()->json([
                'message' => 'Apoyo editado exitosamente'], 201);

        }catch (Exception $e){
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
    {
        try {
            // Verificar permisos de usuario
            if (Auth::user()->id_rol != 5) {
                return response()->json(["error" => "No tienes permisos para realizar esta acción"], 401);
            }

            // Validar la estructura del request
            $request->validate([
                'empresa.nombre' => 'required|string|max:255',
                'empresa.documento' => 'required|string|max:255',
                'empresa.cargo' => 'required|string|max:255',
                'empresa.razonSocial' => 'required|string|max:255',
                'empresa.url_pagina' => 'required',
                'empresa.telefono' => 'required|string|max:20',
                'empresa.celular' => 'required|string|max:20',
                'empresa.direccion' => 'required|string|max:255',
                'empresa.correo' => 'required|email|max:255',
                'empresa.profesion' => 'required|string|max:255',
                'empresa.experiencia' => 'required|string|max:255',
                'empresa.funciones' => 'required|string|max:255',
                'empresa.id_tipo_documento' => 'required|integer',
                'empresa.id_departamento' => 'required|integer',
                'empresa.id_municipio' => 'required|integer',
                'empresa.id_emprendedor' => 'required|integer',
            ]);

            $empresaexiste = Empresa::where('documento', $request['empresa']['documento'])->first();

            if ($empresaexiste) {
                return response()->json([
                    'error' => 'La empresa ya existe',
                ], 409);
            }

            // Crear la empresa
            $empresa = Empresa::create($request->input('empresa'));

            // Manejar apoyos
            $apoyos = [];
            if ($request->has('apoyos')) {
                foreach ($request['apoyos'] as $apoyo) {
                    $Apoyoenempresaexiste = ApoyoEmpresa::where('id_empresa', $empresa->documento)->first();
                    $nuevoApoyo = ApoyoEmpresa::create([
                        "documento" => $apoyo['documento'],
                        "nombre" => $apoyo['nombre'],
                        "apellido" => $apoyo['apellido'],
                        "cargo" => $apoyo['cargo'],
                        "telefono" => $apoyo['telefono'],
                        "celular" => $apoyo['celular'],
                        "email" => $apoyo['email'],
                        "id_tipo_documento" => $apoyo['id_tipo_documento'],
                        "id_empresa" => $empresa->documento,
                    ]);
                    $apoyos[] = $nuevoApoyo;
                }
            }
        }
            catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

        return response()->json([
            'message' =>  'Empresa creada exitosamente',
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $documento)
{
    // Verifica si el usuario tiene permisos
    if (Auth::user()->id_rol != 5) {
        return response()->json(["error" => "No tienes permisos para acceder a esta ruta"], 401);
    }

    // Busca la empresa por el documento
    $empresa = Empresa::find($documento);

    // Verifica si la empresa existe
    if (!$empresa) {
        return response()->json(['message' => 'Empresa no encontrada'], 404);
    }

    // Actualiza la información de la empresa
    $empresa->update($request->except('apoyo'));

    // Maneja la actualización o creación de registros en `apoyo_empresa`
    if ($request->has('apoyo')) {
        $apoyoData = $request->input('apoyo');

        // Verifica si el campo 'documento' está presente y no es nulo
        if (isset($apoyoData['documento']) && !empty($apoyoData['documento'])) {
            // Busca si ya existe un apoyo con el documento especificado
            $apoyo = ApoyoEmpresa::where('documento', $apoyoData['documento'])->first();

            if ($apoyo) {
                // Actualiza el apoyo si ya existe
                $apoyo->update($apoyoData);
            } else {
                // Crea un nuevo apoyo si no existe
                $nuevoApoyo = new ApoyoEmpresa($apoyoData);
                $nuevoApoyo->id_empresa = $empresa->documento; // Asegúrate de asignar el ID de la empresa
                $nuevoApoyo->save();
            }
        } else {
            // Si no hay datos de apoyo o el documento está vacío, no hacer nada con el apoyo
            // Puedes optar por no hacer nada aquí o manejar un caso especial si es necesario
        }
    }

    return response()->json(["message" => "Empresa actualizada"], 200);
}



    /*
    Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

//creacion de empresa
// {
//     "empresa": {
//         "nombre": "Empresa XYZ",
//         "documento": "1234567890",
//         "cargo": "Director",
//         "razonSocial": "XYZ S.A.",
//         "url_pagina": "http://www.xyz.com",
//         "telefono": "123456789",
//         "celular": "987654321",
//         "direccion": "Calle 123 # 45-67",
//         "correo": "contacto@xyz.com",
//         "profesion": "Ingeniero",
//         "experiencia": "10 años",
//         "funciones": "Gerencia y administración",
//         "id_tipo_documento": 1,
//         "id_municipio": "Abejorral",
//         "id_emprendedor": "1000"
//     },
//     "apoyos": [
//         {
//             "documento": "0987654321",
//             "nombre": "John",
//             "apellido": "Doe",
//             "cargo": "Asistente",
//             "telefono": "123456789",
//             "celular": "987654321",
//             "email": "johndoe@example.com",
//             "id_tipo_documento": 1
//         },
//         {
//             "documento": "1122334455",
//             "nombre": "Jane",
//             "apellido": "Smith",
//             "cargo": "Contadora",
//             "telefono": "123456789",
//             "celular": "987654321",
//             "email": "janesmith@example.com",
//             "id_tipo_documento": 2
//         }
//     ]
// }

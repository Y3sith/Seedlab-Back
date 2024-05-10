<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class EmprendedorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*muestras las empresas*/
        $empresa = Empresa::paginate(5);
        return new JsonResponse($empresa->items());


    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $enprendedor= 
    }

    /**
     * Display the specified resource.
     */
    public function show($id_emprendedor)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $documento)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

/* 
EJEMPLO CREATE EMPRESA
{
	"nombre": "pedro francisco villamizar almeria", 
	"documento": "123456",
	"cargo": "jefe",
  "razonSocial": "pedrito sas",
  "urlPagina": "www.panaderiadonpedro.com",
	"telefono": "6363636",
	"celular": "3232323233",
	"direccion": "calle 48#25-12",
	"profesion": "independiente",
	"correo": "pedrito@gmail.com",
	"experiencia": "ninguna",
	"funciones": "panadero",
	"id_tipo_documento": 1,
	"id_municipio": 1,
	"id_emprendedor": 123456
}
*/

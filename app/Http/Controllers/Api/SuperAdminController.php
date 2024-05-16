<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Emprendedor;
use Illuminate\Http\Request;
use App\Models\Empresa;



class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emprendedoresConEmpresas = Emprendedor::with('empresas')->get();
        return response()->json($emprendedoresConEmpresas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $superAdmin = SuperAdmin::find($id);
        if(!$superAdmin){
            return response()->json([
               'message' => 'SuperAdmin no encontrado'], 404);
        }
        $superAdmin->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
        ]);
        return response()->json(['message' => 'SuperAdmin actualizado', $superAdmin, 200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $superAdmin = SuperAdmin::find($id);
        if(!$superAdmin){
            return response()->json([
               'message' => 'SuperAdmin no encontrado'
            ], 404);
        }
        $superAdmin->update([
            'estado' => 0,
        ]);
    }
}

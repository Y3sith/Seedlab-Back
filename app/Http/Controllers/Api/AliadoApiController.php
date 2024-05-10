<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aliado;

class AliadoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $aliados = Aliado::whereHas('auth', function ($query) {
            $query->where('estado', 1);
        })->select('nombre', 'descripcion', 'logo', 'ruta_multi')->get();
        return response()->json($aliados);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asesor;
use Illuminate\Support\Facades\File;

class AsesorSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Asesor::create([
            "id" => "1",
            "nombre" => "Asesor",
            "apellido" => "Prueba",
            "documento" => "000000001",
            "fecha_nac" => "2000-05-21",
            "id_aliado" => "1",
            "id_tipo_documento" => "1",
            "id_departamento" => "27",
            "id_municipio" => "866",
            //"email"=>"",
            "direccion" => "Dirección por defecto",
            "genero" => "Masculino",
            "celular" => "0000000000",
            "id_autentication" => "4",
            //"id_aliado"=> "1",
        ]);
    }
}

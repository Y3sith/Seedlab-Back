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
            "nombre" => "Juan",
            "apellido" => "Perez",
            "documento" => "N/A",
            "fecha_nac" => "2000-05-21",
            "id_aliado" => "1",
            "id_tipo_documento" => "1",
            "id_departamento" => "27",
            "id_municipio" => "866",
            //"email"=>"",
            "direccion" => "N/A",
            "genero" => "N/A",
            "celular" => "N/A",
            "id_autentication" => "4",
            //"id_aliado"=> "1",
        ]);
    }
}

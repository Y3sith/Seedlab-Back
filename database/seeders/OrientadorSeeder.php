<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Orientador;
use Illuminate\Support\Facades\File;

class OrientadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Orientador::create([
            "nombre"=> "David",
            "apellido"=> "Hernandez",
            "documento"=> "213456",
            "fecha_nac"=>"2024-05-21",
            "id_tipo_documento"=>"1",
            "id_departamento"=>"27",
            "id_municipio"=>"866",
            "direccion"=>"N/A",
            "genero"=>"N/A",
            "celular"=> "N/A",
            "id_autentication"=> "2",
        ]); 
    }
}

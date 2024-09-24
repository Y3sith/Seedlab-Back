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
            "nombre"=> "Orientador",
            "apellido"=> "Prueba",
            "documento"=> "0000000010",
            "fecha_nac"=>"2000-05-21",
            "id_tipo_documento"=>"1",
            "id_departamento"=>"27",
            "id_municipio"=>"866",
            "direccion"=>"Direccion por defecto",
            "genero"=>"Masculino",
            "celular"=> "333333333",
            "id_autentication"=> "2",
        ]); 
    }
}

<?php

namespace Database\Seeders;

use App\Models\Emprendedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmprendedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Emprendedor::create([
            "documento"=> "1098476011",
            "nombre"=> "marly",
            "apellido"=> "rangel",
            "imagen_perfil"=>"",
            "celular"=> "3122231313",
            "genero"=> "Femenino",
            "fecha_nac"=> "1998-06-03",
            "direccion"=>"manzaka k",
            "email_verified_at"=>"2024/05/17",
            "cod_ver"=> "153567",
            "id_autentication"=> 5,
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866
        ]);
        Emprendedor::create([
            "documento"=> "28358568",
            "nombre"=> "heidy",
            "apellido"=> "ortega",
            "imagen_perfil"=>"",
            "celular"=> "312444444",
            "genero"=> "Otro",
            "fecha_nac"=> "1998-06-03",
            "direccion"=>"manzaka k",
            "email_verified_at"=>"2024/05/17",
            "cod_ver"=> "183567",
            "id_autentication"=> 16,
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866
        ]);
        Emprendedor::create([
            "documento"=> "10101010",
            "nombre"=> "uriel",
            "apellido"=> "stefano",
            "imagen_perfil"=>"",
            "celular"=> "3122231313",
            "genero"=> "Masculino",
            "fecha_nac"=> "1998-06-03",
            "direccion"=>"manzaka k",
            "email_verified_at"=>"2024/05/17",
            "cod_ver"=> "113562",
            "id_autentication"=> 15,
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866
        ]);
        
    }
}

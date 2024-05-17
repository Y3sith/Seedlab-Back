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
            "celular"=> "3122231313",
            "genero"=> "f",
            "fecha_nac"=> "1998-06-03",
            "direccion"=>"manzaka k",
            "email_verified_at"=>"2024/05/17",
            "cod_ver"=> null,
            "id_autentication"=> 5,
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866
        ]);
        Emprendedor::create([
            "documento"=> "28358568",
            "nombre"=> "heidy",
            "apellido"=> "ortega",
            "celular"=> "312444444",
            "genero"=> "f",
            "fecha_nac"=> "1998-06-03",
            "direccion"=>"manzaka k",
            "email_verified_at"=>null,
            "cod_ver"=> null,
            "id_autentication"=> 3,
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866
        ]);
        Emprendedor::create([
            "documento"=> "10101010",
            "nombre"=> "uriel",
            "apellido"=> "stefano",
            "celular"=> "3122231313",
            "genero"=> "f",
            "fecha_nac"=> "1998-06-03",
            "direccion"=>"manzaka k",
            "email_verified_at"=>null,
            "cod_ver"=> null,
            "id_autentication"=> 2,
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866
        ]);
        
    }
}

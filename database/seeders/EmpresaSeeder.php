<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::create([
            "documento"=> "1",
            "nombre"=> "pollos marly",
            "cargo"=> "jefe",
            "razonSocial"=> "varias cosas",
            "url_pagina"=> "www.pollos.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "pollos@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866,
            "id_emprendedor"=> "1098476011"
        ]);
        Empresa::create([
            "documento"=> "2",
            "nombre"=> "algo marly",
            "cargo"=> "jefe",
            "razonSocial"=> "varias cosas",
            "url_pagina"=> "www.algo.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "algo@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866,
            "id_emprendedor"=> "1098476011"
        ]);


        Empresa::create([
            "documento"=> "3",
            "nombre"=> "papas",
            "cargo"=> "jefe",
            "razonSocial"=> "muchas cosas",
            "url_pagina"=> "www.papas.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "papas@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866,
            "id_emprendedor"=> "28358568"
        ]);

        Empresa::create([
            "documento"=> "4",
            "nombre"=> "papas2",
            "cargo"=> "jefe",
            "razonSocial"=> "muchas cosas",
            "url_pagina"=> "www.papas2.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "papas2@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866,
            "id_emprendedor"=> "28358568"
        ]);


        Empresa::create([
            "documento"=> "5",
            "nombre"=> "balon",
            "cargo"=> "jefe",
            "razonSocial"=> " cosas",
            "url_pagina"=> "www.balon.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "balon@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866,
            "id_emprendedor"=> "10101010"
        ]);

        Empresa::create([
            "documento"=> "6",
            "nombre"=> "popo",
            "cargo"=> "jefe",
            "razonSocial"=> " cosas",
            "url_pagina"=> "www.popo.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "popo@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_municipio"=> 866,
            "id_emprendedor"=> "10101010"
        ]);
    }
}

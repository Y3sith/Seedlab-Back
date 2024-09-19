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
            "documento"=> "12345678",
            "nombre"=> "Lenovo",
            "cargo"=> "jefe",
            "razonSocial"=> "varias cosas",
            "url_pagina"=> "www.lenovo.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "Ing Sistemas",
            "correo"=> "lenovo@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866,
            "id_emprendedor"=> "1098476011"
        ]);
        Empresa::create([
            "documento"=> "223456789",
            "nombre"=> "Dell",
            "cargo"=> "jefe",
            "razonSocial"=> "varias cosas",
            "url_pagina"=> "www.dell.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing electonico",
            "correo"=> "algo@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866,
            "id_emprendedor"=> "1098476011"
        ]);


        Empresa::create([
            "documento"=> "34567891",
            "nombre"=> "Homecenter",
            "cargo"=> "jefe",
            "razonSocial"=> "muchas cosas",
            "url_pagina"=> "www.homecenter.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing industrial",
            "correo"=> "homecenter@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866,
            "id_emprendedor"=> "28358568"
        ]);

        Empresa::create([
            "documento"=> "456789123",
            "nombre"=> "FritoLay",
            "cargo"=> "jefe",
            "razonSocial"=> "muchas cosas",
            "url_pagina"=> "www.fritolay.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "papas2@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866,
            "id_emprendedor"=> "28358568"
        ]);


        Empresa::create([
            "documento"=> "567891234",
            "nombre"=> "Sony",
            "cargo"=> "jefe",
            "razonSocial"=> " cosas",
            "url_pagina"=> "www.sony.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "balon@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866,
            "id_emprendedor"=> "10101010"
        ]);

        Empresa::create([
            "documento"=> "678912345",
            "nombre"=> "Xiaomi",
            "cargo"=> "jefe",
            "razonSocial"=> " cosas",
            "url_pagina"=> "www.xiaomi.com",
            "telefono"=> "60121221",
            "celular"=> "3122231313",
            "direccion"=> "manzana k casa 152",
            "profesion"=> "ing ambiental",
            "correo"=> "xiaomi@gmail.com",
            "experiencia"=> "nada",
            "funciones"=> "jefe",
            "id_tipo_documento"=> 1,
            "id_departamento"=> 27,
            "id_municipio"=> 866,
            "id_emprendedor"=> "10101010"
        ]);
    }
}

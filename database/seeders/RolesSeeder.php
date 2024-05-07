<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create(['nombre'=>'SuperAdmin']);
        Rol::create(['nombre'=>'Orientador']);
        Rol::create(['nombre'=>'Aliado']);
        Rol::create(['nombre'=>'Asesor']);
        Rol::create(['nombre'=>'Emprendedor']);
    }
}

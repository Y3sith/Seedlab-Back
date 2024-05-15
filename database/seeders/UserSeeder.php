<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'superadmin@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 1,
        ]);

        User::create([
            'email' => 'orientador@orientador.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 2,
        ]);

        User::create([
            'email' => 'aliado@aliado.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'asesor@asesor.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 4,
        ]);

        User::create([
            'email' => 'emprendedor@emprendedor.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 5,
        ]);
    }
}

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
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 5,
        ]);

        User::create([
            'email' => 'admin2@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 5,
        ]);

        User::create([
            'email' => 'admin3@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 5,
        ]);

        //aliados auth
        User::create([
            'email' => 'Ecopetrol@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'Imebu@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'Camaradecomercio@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'Otri@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);
        User::create([
            'email' => 'Unab@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'Ucc@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);
        User::create([
            'email' => 'C-emprende@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'Tecnoparque@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);
        User::create([
            'email' => 'Innpulsa@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);
    }
}

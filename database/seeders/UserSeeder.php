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
            'id_rol' => 1,
        ]);

        User::create([
            'email' => 'admin2@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 3,
        ]);

        User::create([
            'email' => 'admin3@admin.com',
            'password' => bcrypt('123456'),
            'estado' => 1,
            'id_rol' => 2,
        ]);
    }
}

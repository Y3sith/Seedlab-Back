<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(DepartamentoSeeder::class);
        $this->call(MunicipiosSeeder::class);
        $this->call(TipodocumentoSeeder::class);
        $this->call(RolesSeeder::class); 
        $this->call(SeccionSeeder::class);
        $this->call(PreguntasSeeder::class);
        $this->call(SubpreguntaSeeder::class);
    }
}

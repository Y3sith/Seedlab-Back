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
        $this->call(RolesSeeder::class); 
        $this->call(UserSeeder::class);
        $this->call(DepartamentoSeeder::class);
        $this->call(MunicipiosSeeder::class);
        $this->call(TipodocumentoSeeder::class);
        $this->call(SeccionSeeder::class);
        $this->call(PreguntasSeeder::class);
        $this->call(TipodatoSeeder::class);
        $this->call(SubpreguntaSeeder::class);
        $this->call(EmprendedorSeeder::class);
        $this->call(EmpresaSeeder::class);
        $this->call(AliadoSeeder::class);
        $this->call(AsesoriaSeeder::class);


        
    }
}

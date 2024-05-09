<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoDato;

class TipodatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDato::create(['nombre' => 'Video']);
        TipoDato::create(['nombre' => 'Multimedia']);
        TipoDato::create(['nombre' => 'Imagen']);
        TipoDato::create(['nombre' => 'Pdf']);
        TipoDato::create(['nombre' => 'Texto']);
    }
}

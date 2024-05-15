<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoDocumento;

class TipodocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipo_de_documento = [
            'Cedula de ciudadania',
            'Cedula de extranjeria',
            'Permiso especial de permanencia'   
        ];

        foreach ($tipo_de_documento  as $tipo_de_documento ) {
            TipoDocumento::create(['nombre' => $tipo_de_documento ]);
        }
    }
}

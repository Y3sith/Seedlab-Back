<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/json/tb_ciudad_departamento.json'));

    foreach ($data as $departamentoData){
        // Obtener el departamento_id del objeto del JSON
        $departamento_id = $departamentoData->id;

        Departamento::create([
            'id' => $departamento_id+1,
            'name' => $this->corregir_caracteres($departamentoData->departamento),
        ]);
    }
}    
        
// Función para corregir caracteres especiales
private function corregir_caracteres($cadena) {
    // Reemplazar los caracteres Unicode escapados con sus equivalentes
    $cadena_corregida = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }, $cadena);

    // Reemplazar otros caracteres especiales comunes
    $replacements = array(
        '\u00c1' => 'Á', '\u00e1' => 'á', '\u00c9' => 'É', '\u00e9' => 'é',
        '\u00cd' => 'Í', '\u00ed' => 'í', '\u00d3' => 'Ó', '\u00f3' => 'ó',
        '\u00da' => 'Ú', '\u00fa' => 'ú', '\u00d1' => 'Ñ', '\u00f1' => 'ñ',
        '\u00fc' => 'ü', '\u00bf' => '¿', '\u00a1' => '¡',
    );

    $cadena_corregida = strtr($cadena_corregida, $replacements);
    
    return $cadena_corregida;
}
}

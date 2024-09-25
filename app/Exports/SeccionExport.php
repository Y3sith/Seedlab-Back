<?php

namespace App\Exports;

use App\Models\Respuesta;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class SeccionExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $resultados;

    public function __construct(array $resultados)
    {
        $this->resultados = $resultados;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Agrupar los resultados por sección
        $secciones = array_unique(array_column($this->resultados, 'seccion'));

        foreach ($secciones as $index => $seccion) {
            $datosSeccion = array_filter($this->resultados, function ($item) use ($seccion) {
                return $item['seccion'] === $seccion;
            });
    
            // Pasar un booleano indicando si es la primera hoja
            $isFirstSheet = $index === 0;
            $sheets[] = new SeccionSheet($datosSeccion, $seccion, $isFirstSheet);
        }

        return $sheets;
    }
}


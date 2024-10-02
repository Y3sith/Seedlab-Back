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
        // Inicializa la clase con un array de resultados
        $this->resultados = $resultados;
    }

    public function sheets(): array
    {
        // Verifica si hay resultados
        if (empty($this->resultados)) {
            // Puedes lanzar una excepción o manejarlo de alguna otra manera
            throw new \Exception('No hay datos disponibles para exportar');
        }

        $sheets = []; // Inicializa un array para almacenar las hojas

        // Agrupar los resultados por sección
        $secciones = array_unique(array_column($this->resultados, 'seccion'));

        foreach ($secciones as $index => $seccion) {
            // Filtrar los resultados para la sección actual
            $datosSeccion = array_filter($this->resultados, function ($item) use ($seccion) {
                return $item['seccion'] === $seccion; // Retorna solo los ítems de la sección actual
            });

            // Pasar un booleano indicando si es la primera hoja
            $isFirstSheet = $index === 0;
            $sheets[] = new SeccionSheet($datosSeccion, $seccion, $isFirstSheet); // Crea una nueva hoja para la sección y la añade al array
        }

        return $sheets; // Devuelve el array de hojas
    }
}


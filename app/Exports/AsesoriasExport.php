<?php

namespace App\Exports;

use App\Models\Asesoria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AsesoriasExport implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $tipo_reporte; // Tipo de reporte para filtrar datos
    protected $fecha_inicio; // Fecha de inicio para el rango de consulta
    protected $fecha_fin;    // Fecha de fin para el rango de consulta

    // Constructor que inicializa las propiedades con los parámetros recibidos
    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin)
    {
        $this->tipo_reporte = $tipo_reporte; // Asigna el tipo de reporte
        $this->fecha_inicio = $fecha_inicio; // Asigna la fecha de inicio
        $this->fecha_fin = $fecha_fin;       // Asigna la fecha de fin
    }

    // Método que obtiene la colección de datos para la exportación
    public function collection()
    {
        // Construcción de la consulta para obtener datos de la tabla especificada en $tipo_reporte
        $query = DB::table($this->tipo_reporte)
            ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id') // Join con la tabla 'aliado'
            ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento') // Join con la tabla 'emprendedor'
            ->select(
                "$this->tipo_reporte.Nombre_sol", "$this->tipo_reporte.notas", "$this->tipo_reporte.fecha" ,  // Selecciona todos los campos de la tabla de asesorías
                'aliado.nombre as nombre_aliado', // Nombre del aliado
                'emprendedor.nombre as nombre_emprendedor', // Nombre del emprendedor
                'emprendedor.documento' // Documento del emprendedor
            );

        // Filtrar por rango de fechas solo si están definidas
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('asesoria.fecha', [$this->fecha_inicio, $this->fecha_fin]); // Aplica el filtro de fechas
        }

        return $query->get(); // Retorna los resultados de la consulta
    }

    // Método que define los encabezados de la exportación
    public function headings(): array
    {
        return [
            'Nombre Asesoria',           // Nombre de la asesoría
            'Descripción',               // Descripción de la asesoría
            'Fecha',                     // Fecha de la asesoría
            'Nombre Aliado',             // Nombre del aliado
            'Nombre Emprendedor',        // Nombre del emprendedor
            'Documento Emprendedor',     // Documento del emprendedor
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $columns = ['A', 'B', 'C', 'D', 'E', 'F','G','H','I','J','K','L']; 
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Aplicar estilos adicionales si es necesario
                $sheet->getStyle('A1:L1')->getFont()->setBold(true);
                $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

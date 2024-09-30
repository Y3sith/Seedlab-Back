<?php

namespace App\Exports;

use App\Models\Asesoria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AsesoriasAliadosExport implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $id_aliado;
    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    // Constructor que inicializa las propiedades con los parámetros recibidos
    public function __construct($id_aliado, $tipo_reporte, $fecha_inicio, $fecha_fin)
    {
        $this->id_aliado = $id_aliado;
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    // Método que obtiene la colección de datos para la exportación
    public function collection()
    {
        // Construcción de la consulta para obtener datos de asesorías
        $query = DB::table('asesoria')
            ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id') // Join con la tabla 'aliado'
            ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento') // Join con la tabla 'emprendedor'
            ->select(
                'asesoria.Nombre_sol', // Nombre de la asesoría
                'asesoria.notas',      // Notas de la asesoría
                'asesoria.fecha',      // Fecha de la asesoría
                'emprendedor.nombre as nombre_emprendedor', // Nombre del emprendedor
                'emprendedor.documento', // Documento del emprendedor
                'aliado.nombre as nombre_aliado' // Nombre del aliado
            )
            ->where('asesoria.id_aliado', $this->id_aliado); // Filtrar por el ID del aliado

        // Filtrar por rango de fechas solo si están definidas
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('asesoria.fecha', [$this->fecha_inicio, $this->fecha_fin]);
        }

        return $query->get();
    }

    // Método que define los encabezados de la exportación
    public function headings(): array
    {
        return [
            'Nombre Asesoria',
            'Descripción',
            'Fecha',
            'Emprendedor Solicitante',
            'Documento Emprendedor',
            'Nombre Aliado'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $columns = ['A', 'B', 'C', 'D', 'E','F']; 
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Aplicar estilos adicionales si es necesario
                $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

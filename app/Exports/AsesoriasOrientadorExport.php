<?php

namespace App\Exports;

use App\Models\Asesoria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AsesoriasOrientadorExport implements FromCollection, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin)
    {
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        $query =  DB::table('asesoria')
            ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
            ->select('asesoria.Nombre_sol', 'asesoria.notas', 'asesoria.fecha', 'emprendedor.nombre as nombre_emprendedor', 'emprendedor.documento')
            ->where('isorientador', 1);
            
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('asesoria.fecha', [$this->fecha_inicio, $this->fecha_fin]);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nombre Asesoria',
            'Descripción',
            'Fecha',
            'Emprendedor Solcitante',
            'Documento Emprendedor'
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $columns = ['A', 'B', 'C', 'D', 'E']; // Asume que tienes cinco columnas
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Aplicar estilos adicionales si es necesario
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}

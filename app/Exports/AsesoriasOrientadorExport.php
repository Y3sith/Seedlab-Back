<?php

namespace App\Exports;

use App\Models\Asesoria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsesoriasOrientadorExport implements FromCollection
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
            'ID',
            'Nombre Asesoria',
            'Descripción',
            'Fecha',
            'Emprendedor Solcitante',
            'Documento Emprendedor'
        ];
    }
}

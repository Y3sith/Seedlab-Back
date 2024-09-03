<?php

namespace App\Exports;

use App\Models\Asesoria;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsesoriasExport implements FromCollection, WithHeadings
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
        $query = DB::table($this->tipo_reporte)
        ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
        ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
        ->select("{$this->tipo_reporte}.*", 'aliado.nombre as nombre_aliado', 'emprendedor.nombre as nombre_emprendedor', 'emprendedor.documento');

        if($this->fecha_inicio && $this->fecha_fin){
            $query->whereBetween('asesoria.fecha', [$this->fecha_inicio, $this->fecha_fin]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Emprendedor',
            'Documento Emprendedor',
            'Nombre Solicitud',
            'Descripción',
            'Fecha',
            'Nombre Aliado',
        ];
    }
}

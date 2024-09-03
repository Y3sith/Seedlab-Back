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

    protected $fech_inicio;
    protected $fech_fin;

    public function __construct($fech_inicio, $fech_fin)
    {
        $this->fech_inicio = $fech_inicio;
        $this->fech_fin = $fech_fin;
    }

    public function collection()
    {
        $query = DB::table('asesoria')
        ->join('aliado', 'asesoria.id_aliado', '=', 'aliado.id')
        ->join('emprendedor', 'asesoria.doc_emprendedor', '=', 'emprendedor.documento')
        ->select('asesoria.*', 'aliado.nombre as nombre_aliado', 'emprendedor.nombre as nombre_emprendedor', 'emprendedor.documento');

        if($this->fech_inicio && $this->fech_fin){
            $query->whereBetween('asesoria.fecha', [$this->fech_inicio, $this->fech_fin]);
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

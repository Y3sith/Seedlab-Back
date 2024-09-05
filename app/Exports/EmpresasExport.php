<?php

namespace App\Exports;

use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpresasExport implements FromCollection, WithHeadings
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
            ->join('emprendedor', 'empresa.id_emprendedor', '=', 'emprendedor.documento')
            ->select("{$this->tipo_reporte}.*", 'emprendedor.documento', 'emprendedor.nombre', 'emprendedor.apellido', 'emprendedor.celular');

        // Filtrar por rango de fechas solo si están definidos ambos
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('empresa.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]);
        }

        return $query->get();
    }


    public function headings(): array
    {
        return [
            'Documento',
            'Nombre Empresa',
            'Razon Social',
            'Telefono',
            'Celular',
            'Dirección',
            'Correo',
            'Pagína Web',
            'Fecha Registro',
            'Departamento',
            'Municipio',
            'Nombre Emprendedor',
            'Apellido Emprendedor',
            'Documento Emprendedor',
            'N° Celular Emprendedor',
        ];
    }
}

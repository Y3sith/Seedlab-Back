<?php

namespace App\Exports;

use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class EmpresasExport implements FromCollection, WithHeadings, WithEvents
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
            ->join('departamentos', 'empresa.id_departamento', 'departamentos.id')
            ->join('municipios', 'empresa.id_municipio', '=', 'municipios.id')
            ->select("$this->tipo_reporte.documento", "$this->tipo_reporte.nombre","$this->tipo_reporte.razonSocial",
            "$this->tipo_reporte.url_pagina","$this->tipo_reporte.celular","$this->tipo_reporte.direccion","$this->tipo_reporte.correo", "$this->tipo_reporte.fecha_registro",
            'departamentos.name','municipios.nombre as municipio',
            'emprendedor.documento as documento_emprendedor', 
            'emprendedor.nombre as nombre_emprendedor', 
            "emprendedor.apellido as apellido_emprendedor", 
            "emprendedor.celular as celular_emprendedor");

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
            'Pagína Web',
            'Celular',
            'Dirección',
            'Correo',
            'Fecha Registro',
            'Departamento',
            'Municipio',
            'Documento Emprendedor',
            'Nombre Emprendedor',
            'Apellido Emprendedor',
            'Celular Emprendedor',
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

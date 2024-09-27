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

    protected $tipo_reporte; // Tipo de reporte para filtrar los datos
    protected $fecha_inicio; // Fecha de inicio para filtrar los resultados
    protected $fecha_fin;    // Fecha de fin para filtrar los resultados

    // Constructor que recibe tipo de reporte y fechas
    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin)
    {
        $this->tipo_reporte = $tipo_reporte; // Asigna el tipo de reporte
        $this->fecha_inicio = $fecha_inicio; // Asigna la fecha de inicio
        $this->fecha_fin = $fecha_fin;       // Asigna la fecha de fin
    }

    // Método que obtiene la colección de datos
    public function collection()
    {
        // Construcción de la consulta para obtener datos de la tabla especificada en $tipo_reporte
        $query = DB::table($this->tipo_reporte)
            ->join('emprendedor', 'empresa.id_emprendedor', '=', 'emprendedor.documento') // Join con la tabla 'emprendedor'
            ->join('departamentos', 'empresa.id_departamento', 'departamentos.id')      // Join con la tabla 'departamentos'
            ->join('municipios', 'empresa.id_municipio', '=', 'municipios.id')        // Join con la tabla 'municipios'
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

    // Método que define los encabezados de la exportación
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
}

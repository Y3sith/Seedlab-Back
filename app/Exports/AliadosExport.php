<?php

namespace App\Exports;

use App\Models\Aliado;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AliadosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($tipo_reporte, $fecha_inicio, $fecha_fin){
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        $query = DB::table('users')
        ->join($this->tipo_reporte, 'users.id', '=', $this->tipo_reporte.'.id_autentication')
        ->select('users.id', 'users.email', 'users.fecha_registro', DB::raw('(CASE WHEN users.estado = 1 THEN "Activo" ELSE "Inactivo" END) as estado'), 
        "{$this->tipo_reporte}.nombre");
        // Filtrar por rango de fechas solo si están definidos ambos
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('users.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]);
        }

        return $query->get();
    }

    public function headings(): array{
        return [
            'Nombre',
            'Correo',
            'Fecha de Registro',
            'Estado',
        ];
    }

    public function map($aliado):array
    {
        return [
            $aliado->nombre,
            $aliado->email,
            $aliado->fecha_registro,
            $aliado->estado,
        ];
    }
}

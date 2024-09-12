<?php

namespace App\Exports;

use App\Models\Asesor;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsesoresAliadosExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $id_aliado;
    protected $tipo_reporte;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($id_aliado, $tipo_reporte, $fecha_inicio, $fecha_fin){
        $this->id_aliado = $id_aliado;
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        $query=DB::table($this->tipo_reporte)
        ->join('aliado', 'asesor.id_aliado', '=', 'aliado.id')
        ->join('users', 'asesor.id_autentication', '=', 'users.id')
        ->select(
            'asesor.nombre',
            'asesor.apellido',
            'asesor.documento',
            'asesor.celular',
            'asesor.fecha_nac',
            'asesor.direccion',
            'users.email',
            'users.fecha_registro',
            DB::raw('(CASE WHEN users.estado = 1 THEN "Activo" ELSE "Inactivo" END) as estado')
        )
        ->where('asesor.id_aliado', $this->id_aliado);
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('users.fecha_registro', [$this->fecha_inicio, $this->fecha_fin]);
        }
        return $query->get();
    }

    public function headings(): array{
        return [
            'Nombre',
            'Apellido',
            'Documento',
            'Celular',
            'Fecha de Nacimiento',
            'Dirección',
            'Correo',
            'Fecha de Registro',
            'Estado',
        ];
    }
}

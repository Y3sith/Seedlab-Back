<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RolesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $rol;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($rol, $fechaInicio, $fechaFin )
    {
        $this->rol = $rol;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;    
    }

    public function collection()
    {
        $query = DB::table('users')
            ->join($this->rol, 'users.id', '=', $this->rol.'.id_autentication')
            ->select('users.id', 'users.email', 'users.fecha_registro', 'users.estado', "{$this->rol}.*")
            ->whereBetween('users.fecha_registro', [$this->fechaInicio, $this->fechaFin]);
            
            return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Email',
            'Fecha de Registro',
            'Estado',
            'Nombre',
            'Apellido',
            'Documento',
            'N° Celular',
            'Genero',
            'Fecha de Nacimiento',
            'Dirección',
        ];
    }

    public function map($emprendedor): array
    {
        return [
            $emprendedor->id,
            $emprendedor->email,
            $emprendedor->fecha_registro,
            $emprendedor->estado,
            $emprendedor->nombre,
            $emprendedor->apellido,
            $emprendedor->documento,
            $emprendedor->celular,
            $emprendedor->genero,
            $emprendedor->fecha_nac,
            $emprendedor->direccion,
        ];
    }
}

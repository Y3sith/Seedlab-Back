<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsesoriaxAsesor extends Model
{
    use HasFactory;

    protected $table = 'asesoriaxasesor';

    protected $fillable = [
        'id_asesoria',
        'id_asesor',
    ];

    public $timestamps = false;


    public function asesoria()
    {
        return $this->belongsTo(Asesoria::class, 'id_asesoria');
    }

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'id_asesor');
    }

  

}

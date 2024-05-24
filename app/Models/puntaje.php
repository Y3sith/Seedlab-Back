<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class puntaje extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'puntaje';

    protected $fillable = [
        'info_gen',
        'info_fin',
        'info_mer',
        'info_op',
        'info_trl',
        'id_empresa',
        'ver_form',
    ];

    public function empresas(){
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}

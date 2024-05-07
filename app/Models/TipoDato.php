<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDato extends Model
{
    use HasFactory;

    protected $fillable = [
        'video',
        'multimedia',
        'imagen',
        'pdf',
        'texto'
    ];

    public $timestamps = false;
}

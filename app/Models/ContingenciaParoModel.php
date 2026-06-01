<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContingenciaParoModel extends Model
{
    protected $table = 'contingencias_paros';
    protected $primaryKey = 'id_contingencia';

    protected $fillable = [
        'fecha',
        'descripcion',
        'bloqueo_total'
    ];
}

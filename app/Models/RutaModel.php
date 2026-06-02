<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaModel extends Model
{
    protected $table = 'rutas';
    protected $primaryKey = 'id_ruta';
    protected $fillable = [
        'id_zona',
        'nombre_ruta',
        'horario_permitido',
        'geom',
        'estado'
    ];

    public function zona()
    {
        return $this->belongsTo(ZonaModel::class, 'id_zona', 'id_zona');
    }
}

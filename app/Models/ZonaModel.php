<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaModel extends Model
{
    protected $table = 'zonas';
    protected $primaryKey = 'id_zona';
    protected $fillable = [
        'nombre_zona',
        'descripcion'
    ];

    public function rutas()
    {
        return $this->hasMany(RutaModel::class, 'id_zona', 'id_zona');
    }
}

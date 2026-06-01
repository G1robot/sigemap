<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroBotaderoModel extends Model
{
    protected $table = 'registro_botadero';
    protected $primaryKey = 'id_botadero';

    protected $fillable = [
        'id_asignacion',
        'hora_descarga',
        'peso_descargado_ton'
    ];

    // Relación: Cada registro de botadero pertenece a un viaje (asignación)
    public function asignacion()
    {
        return $this->belongsTo(AsignacionRutaModel::class, 'id_asignacion', 'id_asignacion');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamionModel extends Model
{
    protected $table = 'camiones';
    protected $primaryKey = 'id_camion';
    protected $fillable = [
        'placa',
        'modelo',
        'capacidad_ton',
        'dimension_tipo',
        'estado_operativo'
    ];

    public function historialMantenimiento()
    {
        return $this->hasMany(HistorialMantenimientoModel::class, 'id_camion', 'id_camion');
    }
}

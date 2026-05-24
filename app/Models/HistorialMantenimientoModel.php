<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialMantenimientoModel extends Model
{
    protected $table = 'historial_mantenimiento';
    protected $primaryKey = 'id_mantenimiento';
    protected $fillable = [
        'id_camion',
        'fecha_ingreso',
        'descripcion'
    ];

    public function camion()
    {
        return $this->belongsTo(CamionModel::class, 'id_camion', 'id_camion');
    }
}

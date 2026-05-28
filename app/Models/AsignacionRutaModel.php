<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionRutaModel extends Model
{
    protected $table = 'asignaciones_ruta';
    protected $primaryKey = 'id_asignacion';

    protected $fillable = [
        'id_camion',
        'id_ruta',
        'fecha',
        'turno',
        'estado_operacion'
    ];

    public function camion()
    {
        return $this->belongsTo(CamionModel::class, 'id_camion', 'id_camion');
    }

    public function ruta()
    {
        return $this->belongsTo(RutaModel::class, 'id_ruta', 'id_ruta');
    }

    public function detallesCuadrilla()
    {
        return $this->hasMany(DetalleCuadrillaModel::class, 'id_asignacion', 'id_asignacion');
    }
}

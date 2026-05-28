<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCuadrillaModel extends Model
{
    protected $table = 'detalle_cuadrilla';
    
    public $incrementing = false; 
    public $timestamps = false; 

    protected $fillable = [
        'id_asignacion',
        'id_usuario',
        'rol_en_viaje',
        'asistio',       
        'hora_marcaje'   
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionRutaModel::class, 'id_asignacion', 'id_asignacion');
    }

    public function usuario()
    {
        return $this->belongsTo(UsuarioModel::class, 'id_usuario', 'id_usuario');
    }
}

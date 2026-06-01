<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoPersonalModel extends Model
{
    protected $table = 'pagos_personal';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_usuario',
        'id_asignacion',
        'monto_pago',
        'fecha'
    ];

    // Relación: Este pago es para un trabajador específico
    public function usuario()
    {
        return $this->belongsTo(UsuarioModel::class, 'id_usuario', 'id_usuario');
    }

    // Relación: Este pago corresponde a un viaje/ruta específica
    public function asignacion()
    {
        return $this->belongsTo(AsignacionRutaModel::class, 'id_asignacion', 'id_asignacion');
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;

class UsuarioModel extends Authenticatable
{
    use Notifiable;
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre_completo', 
        'ci',
        'telefono', 
        'usuario', 
        'password',
        'cargo_base',
        'rol',
        'estado',
        'tarifa_por_viaje'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
}

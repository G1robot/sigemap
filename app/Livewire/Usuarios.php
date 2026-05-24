<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UsuarioModel;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Usuarios extends Component
{
    use WithPagination;
    public $search = '';
    public $showModal = false;

    public $es_usuario_sistema = false;
    
    public $nombre_completo = '';
    public $ci = '';
    public $telefono = '';
    public $usuario = '';
    public $contrasena = '';
    public $contrasena1 = '';
    public $cargo_base = '';
    public $rol = '';
    public $estado = 'Activo';
    public $usuario_id ='';

    protected function rules()
    {
        $reglas = [
            'nombre_completo' => 'required|string|regex:/^[\pL\s]+$/u|max:255',
            'ci' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{6,8}(-[A-Za-z0-9]{1,2})?$/',
                Rule::unique('usuarios', 'ci')->ignore($this->usuario_id, 'id_usuario') 
            ],
            'telefono' => 'required|string|max:20',
            'cargo_base' => 'required|string|max:255',
        ];

        if ($this->es_usuario_sistema) {
            $reglas['usuario'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('usuarios', 'usuario')->ignore($this->usuario_id, 'id_usuario') 
            ];
            $reglas['rol'] = 'required|in:Administrador,Supervisor,Operario';
            
            if (!$this->usuario_id || !empty($this->contrasena)) {
                $reglas['contrasena'] = 'required|string|min:6';
                $reglas['contrasena1'] = 'required|string|same:contrasena';
            }
        }

        return $reglas;
    }

    public function render()
    {
        $search = mb_strtolower(trim($this->search));
        $usuarios = UsuarioModel::whereRaw('LOWER(nombre_completo) LIKE ?', ["%{$search}%"])
            ->orWhereRaw('LOWER(usuario) LIKE ?', ["%{$search}%"])
            ->orderBy('nombre_completo', 'asc')
            ->paginate(10);
            
        return view('livewire.usuarios', compact('usuarios'));
    }

    public function openModal()
    {
        $this->limpiarDatos();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->limpiarDatos();
        $this->resetValidation();
    }

    public function limpiarDatos()
    {
        $this->nombre_completo = '';
        $this->ci = '';
        $this->telefono = '';
        $this->usuario = '';
        $this->contrasena = '';
        $this->contrasena1 = '';
        $this->cargo_base = '';
        $this->rol = '';
        $this->estado = 'Activo';
        $this->usuario_id = '';
        $this->es_usuario_sistema = false;
    }
    
    public function enviarClick()
    {
        $this->validate();

        if ($this->usuario_id) {
            $usuario = UsuarioModel::find($this->usuario_id);
            $usuario->nombre_completo = $this->nombre_completo;
            $usuario->ci = $this->ci;
            $usuario->telefono = $this->telefono;
            $usuario->cargo_base = $this->cargo_base;
            $usuario->estado = $this->estado;

            if ($this->es_usuario_sistema) {
                $usuario->usuario = $this->usuario;
                $usuario->rol = $this->rol;
                if (!empty($this->contrasena)) {
                    $usuario->password = Hash::make($this->contrasena); 
                }
            } else {
                $usuario->usuario = null;
                $usuario->rol = null;
                $usuario->password = null;
            }
            
            $usuario->save();
        } else {
            UsuarioModel::create([
                'nombre_completo' => $this->nombre_completo,
                'ci' => $this->ci,
                'telefono' => $this->telefono,
                'cargo_base' => $this->cargo_base,
                'estado' => $this->estado,
                'usuario' => $this->es_usuario_sistema ? $this->usuario : null,
                'password' => ($this->es_usuario_sistema && $this->contrasena) ? Hash::make($this->contrasena) : null,
                'rol' => $this->es_usuario_sistema ? $this->rol : null,
            ]);
        }

        $this->closeModal();

        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Personal guardado exitosamente'
        ]);
    }

    public function editar($id)
    {
        $usuario = UsuarioModel::findOrFail($id);
        $this->usuario_id = $id;
        $this->nombre_completo = $usuario->nombre_completo;
        $this->ci = $usuario->ci;
        $this->telefono = $usuario->telefono;
        $this->cargo_base = $usuario->cargo_base;
        $this->estado = $usuario->estado;
        
        if ($usuario->usuario != null) {
            $this->es_usuario_sistema = true;
            $this->usuario = $usuario->usuario;
            $this->rol = $usuario->rol;
        } else {
            $this->es_usuario_sistema = false;
        }
        
        $this->showModal = true;
    }   
    
    public function eliminar($id)
    {
        $usuario = UsuarioModel::findOrFail($id);
        $usuario->estado = 'Inactivo';
        $usuario->save();

        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Personal desactivado exitosamente'
        ]);
    }

    public function restaurar($id)
    {
        $usuario = UsuarioModel::findOrFail($id);
        $usuario->estado = 'Activo';
        $usuario->save();

        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Personal reactivado exitosamente'
        ]);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CamionModel;
use App\Models\HistorialMantenimientoModel;
use App\Models\AsignacionRutaModel;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class Camiones extends Component
{
    use WithPagination;
    
    public $search = '';
    
    public $showModal = false;
    public $camion_id = '';
    public $placa = '';
    public $modelo = '';
    public $capacidad_ton = '';
    public $dimension_tipo = '';

    public $showMantenimientoModal = false;
    public $mantenimiento_descripcion = '';
    public $camion_mantenimiento_id = '';

    protected function rules()
    {
        return [
            'placa' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9]{3,4}[ -]?[A-Za-z]{3}$/', 
                Rule::unique('camiones', 'placa')->ignore($this->camion_id, 'id_camion') 
            ],
            'modelo' => 'nullable|string|max:50',
            'capacidad_ton' => 'required|numeric|min:0.1', 
            'dimension_tipo' => 'required|string|max:30',
        ];
    }

    protected $messages = [
        'placa.regex' => 'El formato debe ser válido (Ej: 1234-ABC o 123ABC).',
        'capacidad_ton.min' => 'La capacidad no puede ser menor a 0.1',
    ];

    public function render()
    {
        $search = mb_strtolower(trim($this->search));
        $camiones = CamionModel::whereRaw('LOWER(placa) LIKE ?', ["%{$search}%"])
            ->orWhereRaw('LOWER(modelo) LIKE ?', ["%{$search}%"])
            ->orderBy('estado_operativo', 'asc')
            ->orderBy('id_camion', 'desc')
            ->paginate(10);
            
        return view('livewire.camiones', compact('camiones'));
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
        $this->camion_id = '';
        $this->placa = '';
        $this->modelo = '';
        $this->capacidad_ton = '';
        $this->dimension_tipo = '';
    }
    
    public function enviarClick()
    {
        $this->validate();
        
        $placaLimpia = strtoupper(str_replace([' ', '-'], '', trim($this->placa)));

        if ($this->camion_id) {
            $camion = CamionModel::find($this->camion_id);
            $camion->update([
                'placa' => $placaLimpia,
                'modelo' => $this->modelo,
                'capacidad_ton' => $this->capacidad_ton,
                'dimension_tipo' => $this->dimension_tipo,
            ]);
        } else {
            CamionModel::create([
                'placa' => $placaLimpia,
                'modelo' => $this->modelo,
                'capacidad_ton' => $this->capacidad_ton,
                'dimension_tipo' => $this->dimension_tipo,
                'estado_operativo' => 'Operativo', 
            ]);
        }

        $this->closeModal();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Vehículo guardado exitosamente']);
    }

    public function editar($id)
    {
        $camion = CamionModel::findOrFail($id);
        $this->camion_id = $camion->id_camion;
        $this->placa = $camion->placa;
        $this->modelo = $camion->modelo;
        $this->capacidad_ton = $camion->capacidad_ton;
        $this->dimension_tipo = $camion->dimension_tipo;
        
        $this->showModal = true;
    }   

    public function marcarOperativo($id)
    {
        $camion = CamionModel::findOrFail($id);
        $camion->estado_operativo = 'Operativo';
        $camion->save();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'El camión está operativo nuevamente']);
    }

    private function verificarSiEstaEnRuta($id)
    {
        return AsignacionRutaModel::where('id_camion', $id)
            ->where('estado_operacion', 'En Ruta', 'Programada')
            ->exists();
    }

    public function eliminar($id) 
    {
        if ($this->verificarSiEstaEnRuta($id)) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Denegado: El camión está trabajando (En Ruta) actualmente.']);
            return;
        }

        $camion = CamionModel::findOrFail($id);
        $camion->estado_operativo = 'Fuera de Servicio';
        $camion->save();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Vehículo dado de baja definitiva']);
    }

    public function abrirMantenimiento($id)
    {
        if ($this->verificarSiEstaEnRuta($id)) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Denegado: El camión está trabajando (En Ruta) actualmente.']);
            return;
        }

        $this->camion_mantenimiento_id = $id;
        $this->mantenimiento_descripcion = '';
        $this->showMantenimientoModal = true;
    }

    public function cerrarMantenimiento()
    {
        $this->showMantenimientoModal = false;
        $this->camion_mantenimiento_id = '';
        $this->mantenimiento_descripcion = '';
    }

    public function guardarMantenimiento()
    {
        $this->validate([
            'mantenimiento_descripcion' => 'required|string|max:255'
        ]);

        HistorialMantenimientoModel::create([
            'id_camion' => $this->camion_mantenimiento_id,
            'fecha_ingreso' => Carbon::now()->toDateString(),
            'descripcion' => $this->mantenimiento_descripcion,
        ]);

        $camion = CamionModel::find($this->camion_mantenimiento_id);
        $camion->estado_operativo = 'En Mantenimiento';
        $camion->save();

        $this->cerrarMantenimiento();
        $this->dispatch('toast', ['icon' => 'warning', 'title' => 'Vehículo enviado al taller mecánico']);
    }
}

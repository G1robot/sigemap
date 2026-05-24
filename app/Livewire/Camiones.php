<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CamionModel;
use App\Models\HistorialMantenimientoModel;
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
                Rule::unique('camiones', 'placa')->ignore($this->camion_id, 'id_camion') 
            ],
            'modelo' => 'nullable|string|max:50',
            'capacidad_ton' => 'required|numeric|min:0.1',
            'dimension_tipo' => 'required|string|max:30',
        ];
    }

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
        $placaMayuscula = strtoupper(trim($this->placa));

        if ($this->camion_id) {
            $camion = CamionModel::find($this->camion_id);
            $camion->update([
                'placa' => $placaMayuscula,
                'modelo' => $this->modelo,
                'capacidad_ton' => $this->capacidad_ton,
                'dimension_tipo' => $this->dimension_tipo,
            ]);
        } else {
            CamionModel::create([
                'placa' => $placaMayuscula,
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

    public function eliminar($id) 
    {
        $camion = CamionModel::findOrFail($id);
        $camion->estado_operativo = 'Fuera de Servicio';
        $camion->save();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Vehículo dado de baja definitiva']);
    }

    public function abrirMantenimiento($id)
    {
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
        HistorialMantenimientoModel::create([
            'id_camion' => $this->camion_mantenimiento_id,
            'fecha_ingreso' => Carbon::now()->toDateString(),
            'descripcion' => $this->mantenimiento_descripcion,
        ]);

        $camion = CamionModel::find($this->camion_mantenimiento_id);
        $camion->estado_operativo = 'En Mantenimiento';
        $camion->save();

        $this->cerrarMantenimiento();
        $this->dispatch('toast', ['icon' => 'warning', 'title' => 'Vehículo enviado al taller mecáncio']);
    }
}

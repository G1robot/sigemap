<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ZonaModel;
use App\Models\RutaModel;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Zonas extends Component
{
    use WithPagination;
    
    public $search = '';
    public $showModal = false;

    public $zona_id = '';
    public $nombre_zona = '';
    public $descripcion = '';

    protected function rules()
    {
        return [
            'nombre_zona' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\pN\s\-\.,#()]+$/u', 
                Rule::unique('zonas', 'nombre_zona')->ignore($this->zona_id, 'id_zona') 
            ],
            'descripcion' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'nombre_zona.regex' => 'El nombre contiene caracteres especiales no permitidos.',
        'nombre_zona.unique' => 'Ya existe una zona registrada con este nombre exacto.'
    ];

    public function render()
    {
        $search = mb_strtolower(trim($this->search));
        $zonas = ZonaModel::whereRaw('LOWER(nombre_zona) LIKE ?', ["%{$search}%"])
            ->orderBy('nombre_zona', 'asc')
            ->paginate(10);
            
        return view('livewire.zonas', compact('zonas'));
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
        $this->zona_id = '';
        $this->nombre_zona = '';
        $this->descripcion = '';
    }
    
    public function enviarClick()
    {
        $this->validate();

        if ($this->zona_id) {
            $zona = ZonaModel::find($this->zona_id);
            $zona->update([
                'nombre_zona' => $this->nombre_zona,
                'descripcion' => $this->descripcion,
            ]);
        } else {
            ZonaModel::create([
                'nombre_zona' => $this->nombre_zona,
                'descripcion' => $this->descripcion,
            ]);
        }

        $this->closeModal();
        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Zona territorial guardada exitosamente'
        ]);
    }

    public function editar($id)
    {
        $zona = ZonaModel::findOrFail($id);
        $this->zona_id = $zona->id_zona;
        $this->nombre_zona = $zona->nombre_zona;
        $this->descripcion = $zona->descripcion;
        
        $this->showModal = true;
    }   
    
    public function eliminar($id)
    {
        $rutasActivas = RutaModel::where('id_zona', $id)->count();

        if ($rutasActivas > 0) {
            $this->dispatch('toast', [
                'icon' => 'error', 
                'title' => "Denegado: Esta zona tiene $rutasActivas rutas asociadas. Reasígnalas primero."
            ]);
            return;
        }

        $zona = ZonaModel::findOrFail($id);
        $zona->delete();

        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Zona eliminada del sistema'
        ]);
    }
}

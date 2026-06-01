<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ContingenciaParoModel;
use Illuminate\Validation\Rule;

class Contingencias extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;

    public $id_contingencia;
    public $fecha;
    public $descripcion;
    public $bloqueo_total = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $contingencias = ContingenciaParoModel::where('descripcion', 'ilike', '%' . $this->search . '%')
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return view('livewire.contingencias', compact('contingencias'));
    }

    public function openModal()
    {
        $this->reset(['id_contingencia', 'fecha', 'descripcion', 'bloqueo_total']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function guardar()
    {
        $this->validate([
            'fecha' => [
                'required', 
                'date',
                Rule::unique('contingencias_paros', 'fecha')->ignore($this->id_contingencia, 'id_contingencia')
            ],
            'descripcion' => 'required|string|max:255',
        ], [
            'fecha.unique' => 'Ya existe un registro de contingencia para esta fecha exacta.'
        ]);

        ContingenciaParoModel::updateOrCreate(
            ['id_contingencia' => $this->id_contingencia],
            [
                'fecha' => $this->fecha,
                'descripcion' => $this->descripcion,
                'bloqueo_total' => $this->bloqueo_total ? true : false,
            ]
        );

        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Contingencia registrada correctamente']);
        $this->closeModal();
    }

    public function editar($id)
    {
        $registro = ContingenciaParoModel::findOrFail($id);
        $this->id_contingencia = $registro->id_contingencia;
        $this->fecha = $registro->fecha;
        $this->descripcion = $registro->descripcion;
        $this->bloqueo_total = $registro->bloqueo_total;
        
        $this->showModal = true;
    }

    public function eliminar($id)
    {
        ContingenciaParoModel::findOrFail($id)->delete();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Registro eliminado']);
    }
}

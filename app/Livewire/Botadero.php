<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AsignacionRutaModel;
use App\Models\RegistroBotaderoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Botadero extends Component
{
    public $showModal = false;
    
    public $asignacion_id = '';
    public $peso_descargado = '';
    public $accion_post_descarga = 'retornar';
    
    public $camion_placa = '';
    public $capacidad_maxima = '';
    public $ruta_nombre = '';

    public function render()
    {
        $viajes_activos = AsignacionRutaModel::with(['camion', 'ruta', 'detallesCuadrilla' => function($query) {
            $query->where('rol_en_viaje', 'Chofer')->with('usuario');
        }])
        ->where('estado_operacion', 'En Ruta')
        ->orderBy('fecha', 'asc')
        ->get();
        return view('livewire.botadero', compact('viajes_activos'));
    }

    public function abrirPesaje($id)
    {
        $viaje = AsignacionRutaModel::with('camion', 'ruta')->findOrFail($id);
        
        $this->asignacion_id = $viaje->id_asignacion;
        $this->camion_placa = $viaje->camion->placa;
        $this->capacidad_maxima = $viaje->camion->capacidad_ton;
        $this->ruta_nombre = $viaje->ruta->nombre_ruta ?? 'Ruta Desconocida';

        $this->peso_descargado = $this->capacidad_maxima;
        $this->accion_post_descarga = 'retornar';

        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['asignacion_id', 'peso_descargado', 'accion_post_descarga', 'camion_placa', 'capacidad_maxima', 'ruta_nombre']);
    }

    public function guardarDescarga()
    {
        $this->validate([
            'peso_descargado' => 'required|numeric|min:0.1',
            'accion_post_descarga' => 'required|in:retornar,finalizar'
        ]);

        DB::beginTransaction();
        try {
            RegistroBotaderoModel::create([
                'id_asignacion' => $this->asignacion_id,
                'hora_descarga' => Carbon::now(), 
                'peso_descargado_ton' => $this->peso_descargado
            ]);

            if ($this->accion_post_descarga === 'finalizar') {
                $viaje = AsignacionRutaModel::find($this->asignacion_id);
                $viaje->estado_operacion = 'Finalizada';
                $viaje->save();
                $mensaje = 'Pesaje guardado y turno Finalizado. Camión libre.';
            } else {
                $mensaje = 'Pesaje guardado. El camión retorna a seguir limpiando.';
            }

            DB::commit();
            $this->cerrarModal();
            $this->dispatch('toast', ['icon' => 'success', 'title' => $mensaje]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Error: ' . $e->getMessage()]);
        }
    }
}

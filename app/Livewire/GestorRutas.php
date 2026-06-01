<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ZonaModel;
use App\Models\RutaModel;
use Illuminate\Support\Facades\DB;

class GestorRutas extends Component
{
    use WithPagination;

    public $modo = 'lista';
    public $search = '';

    public $id_zona = '';
    public $nombre_ruta = '';
    public $horario_permitido = '';
    public $coordenadas_geojson = ''; 

    protected $rules = [
        'id_zona' => 'required',
        'nombre_ruta' => 'required|string|max:100',
        'horario_permitido' => 'required|string|max:50',
        'coordenadas_geojson' => 'required|string'
    ];

    protected $messages = [
        'coordenadas_geojson.required' => 'Debes trazar al menos una ruta en el mapa (mínimo 2 puntos).'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = mb_strtolower(trim($this->search));

        $rutas = RutaModel::selectRaw('id_ruta, id_zona, nombre_ruta, horario_permitido, ST_AsGeoJSON(geom) as geom_json')
            ->with('zona')
            ->whereRaw('LOWER(nombre_ruta) LIKE ?', ["%{$search}%"])
            ->orderBy('id_ruta', 'desc')
            ->paginate(8);

        $zonas = ZonaModel::orderBy('nombre_zona', 'asc')->get();

        return view('livewire.gestor-rutas', compact('rutas', 'zonas'));
    }

    public function cambiarModo($nuevoModo)
    {
        $this->modo = $nuevoModo;
        $this->resetValidation();

        if ($this->modo === 'crear') {
            $this->reset(['id_zona', 'nombre_ruta', 'horario_permitido', 'coordenadas_geojson']);
            $this->dispatch('activar-modo-crear');
        } else {
            $this->dispatch('activar-modo-lista');
        }
    }

    public function verMapa($id)
    {
        $ruta = RutaModel::selectRaw('ST_AsGeoJSON(geom) as geom_json')->findOrFail($id);
        
        $this->dispatch('dibujar-ruta-visor', geojson: $ruta->geom_json);
    }

    public function guardarRuta()
    {
        $this->validate();

        RutaModel::create([
            'id_zona' => $this->id_zona,
            'nombre_ruta' => $this->nombre_ruta,
            'horario_permitido' => $this->horario_permitido,
            'geom' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$this->coordenadas_geojson}'), 4326)")
        ]);

        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Ruta trazada y guardada en el sistema']);
        
        $this->cambiarModo('lista');
    }

    public function eliminar($id)
    {
        RutaModel::findOrFail($id)->delete();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Ruta eliminada geográficamente']);
        $this->dispatch('activar-modo-lista'); 
    }
}

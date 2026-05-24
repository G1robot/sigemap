<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ZonaModel;
use App\Models\RutaModel;
use Illuminate\Support\Facades\DB;

class Rutas extends Component
{
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
        'coordenadas_geojson.required' => 'Debes trazar al menos una ruta en el mapa.'
    ];

    public function render()
    {
        $zonas = ZonaModel::orderBy('nombre_zona', 'asc')->get();
        return view('livewire.rutas', compact('zonas'));
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

        $this->reset(['id_zona', 'nombre_ruta', 'horario_permitido', 'coordenadas_geojson']);
        
        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Ruta trazada y guardada en el sistema'
        ]);

        $this->dispatch('limpiar-mapa');
    }
}

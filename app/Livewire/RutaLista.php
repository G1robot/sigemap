<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RutaModel;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class RutaLista extends Component
{
    use WithPagination;

    public $search = '';
    
    public $showMapModal = false;
    public $nombre_ruta_ver = '';
    public $zona_ruta_ver = '';
    public $geojson_ver = '';

    public function render()
    {
        $search = mb_strtolower(trim($this->search));

        $rutas = RutaModel::selectRaw('id_ruta, id_zona, nombre_ruta, horario_permitido, ST_AsGeoJSON(geom) as geom_json')
            ->with('zona')
            ->whereRaw('LOWER(nombre_ruta) LIKE ?', ["%{$search}%"])
            ->orderBy('id_ruta', 'desc')
            ->paginate(10);

        return view('livewire.ruta-lista', compact('rutas'));
    }
    public function verMapa($id)
    {
        $ruta = RutaModel::selectRaw('id_ruta, id_zona, nombre_ruta, horario_permitido, ST_AsGeoJSON(geom) as geom_json')
            ->with('zona')
            ->findOrFail($id);

        $this->nombre_ruta_ver = $ruta->nombre_ruta;
        $this->zona_ruta_ver = $ruta->zona?->nombre_zona ?: 'Sin Zona';
        
        $this->geojson_ver = $ruta->geom_json; 

        $this->showMapModal = true;

        $this->dispatch('dibujar-ruta-guardada', geojson: $this->geojson_ver);
    }

    public function closeModal()
    {
        $this->showMapModal = false;
        $this->nombre_ruta_ver = '';
        $this->zona_ruta_ver = '';
        $this->geojson_ver = '';
    }

    public function eliminar($id)
    {
        $ruta = RutaModel::findOrFail($id);
        $ruta->delete();

        $this->dispatch('toast', [
            'icon' => 'success', 
            'title' => 'Ruta eliminada geográficamente del sistema'
        ]);
    }
}

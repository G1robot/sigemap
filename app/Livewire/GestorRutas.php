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

    public $ruta_id = '';
    public $id_zona = '';
    public $nombre_ruta = '';
    public $horario_permitido = '';
    public $estado = 'Activo';
    public $coordenadas_geojson = '';

    protected $rules = [
        'id_zona' => 'required',
        'nombre_ruta' => 'required|string|regex:/^[\pL\pN\s\-\.,#]+$/u|max:100',
        'horario_permitido' => 'required|string|max:50',
        'coordenadas_geojson' => 'required|string'
    ];

    protected $messages = [
        'coordenadas_geojson.required' => 'Debes trazar al menos una ruta en el mapa (mínimo 2 puntos).',
        'nombre_ruta.regex' => 'El nombre contiene caracteres no permitidos.'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = mb_strtolower(trim($this->search));

        $rutas = RutaModel::selectRaw('id_ruta, id_zona, nombre_ruta, horario_permitido, estado, ST_AsGeoJSON(geom) as geom_json')
            ->with('zona')
            ->whereRaw('LOWER(nombre_ruta) LIKE ?', ["%{$search}%"])
            ->orderBy('estado', 'asc') 
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
            $this->reset(['ruta_id', 'id_zona', 'nombre_ruta', 'horario_permitido', 'estado', 'coordenadas_geojson']);
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

    public function editar($id)
    {
        $ruta = RutaModel::selectRaw('id_ruta, id_zona, nombre_ruta, horario_permitido, estado, ST_AsGeoJSON(geom) as geom_json')->findOrFail($id);
        
        $this->ruta_id = $ruta->id_ruta;
        $this->id_zona = $ruta->id_zona;
        $this->nombre_ruta = $ruta->nombre_ruta;
        $this->horario_permitido = $ruta->horario_permitido;
        $this->estado = $ruta->estado;
        $this->coordenadas_geojson = $ruta->geom_json;

        $this->modo = 'crear';
        $this->dispatch('editar-ruta-mapa', geojson: $ruta->geom_json);
    }

    public function guardarRuta()
    {
        $this->validate();

        if ($this->ruta_id) {
            RutaModel::where('id_ruta', $this->ruta_id)->update([
                'id_zona' => $this->id_zona,
                'nombre_ruta' => $this->nombre_ruta,
                'horario_permitido' => $this->horario_permitido,
                'estado' => $this->estado,
                'geom' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$this->coordenadas_geojson}'), 4326)")
            ]);
            $mensaje = 'Ruta actualizada exitosamente.';
        } else {
            RutaModel::create([
                'id_zona' => $this->id_zona,
                'nombre_ruta' => $this->nombre_ruta,
                'horario_permitido' => $this->horario_permitido,
                'estado' => 'Activo',
                'geom' => DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$this->coordenadas_geojson}'), 4326)")
            ]);
            $mensaje = 'Ruta trazada y guardada en el sistema.';
        }

        $this->dispatch('toast', ['icon' => 'success', 'title' => $mensaje]);
        $this->cambiarModo('lista');
    }

    public function eliminar($id)
    {
        $ruta = RutaModel::findOrFail($id);
        $ruta->estado = 'Inactivo';
        $ruta->save();

        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Ruta desactivada']);
        $this->dispatch('activar-modo-lista'); 
    }

    public function restaurar($id)
    {
        $ruta = RutaModel::findOrFail($id);
        $ruta->estado = 'Activo';
        $ruta->save();

        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Ruta reactivada operativamente']);
        $this->dispatch('activar-modo-lista'); 
    }
}

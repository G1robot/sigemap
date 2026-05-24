<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function zonas()
    {
        return view('rutas.zonas');
    }
    
    public function index()
    {
        return view('rutas.index');
    }

    public function rutaLista()
    {
        return view('rutas.ruta_lista');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function index()
    {
        return view('asignaciones.index');
    }
}

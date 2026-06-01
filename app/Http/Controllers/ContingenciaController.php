<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContingenciaController extends Controller
{
    public function index()
    {
        return view('contingencias.index');
    }
}

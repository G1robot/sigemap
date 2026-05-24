<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CamionController extends Controller
{
    public function index()
    {
        return view('camiones.index');
    }
}

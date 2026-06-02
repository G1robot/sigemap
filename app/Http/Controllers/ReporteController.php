<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function dashboard()
    {
        return view('reportes.dashboard');
    }

    public function reporteCamiones()
    {
        return view('reportes.reporte_camiones');
    }

    public function reporteFinanciero()
    {
        return view('reportes.reporte_financiero');
    }
}

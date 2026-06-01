<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotaderoController extends Controller
{
    public function index()
    {
        return view('botadero.index');
    }
}

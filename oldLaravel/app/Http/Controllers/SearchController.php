<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Redirige a la vista principal de tareas si no hay implementación específica.
        return redirect()->route('home');
    }
}

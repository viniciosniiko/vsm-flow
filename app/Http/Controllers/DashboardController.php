<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fluxo;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'totalClientes' => Cliente::count(),
            'totalFluxos' => Fluxo::count(),
            'fluxosRascunho' => Fluxo::where('status', 'rascunho')->count(),
            'fluxosAprovados' => Fluxo::where('status', 'aprovado')->count(),
            'fluxos' => Fluxo::with('cliente')->latest()->take(6)->get(),
        ]);
    }
}
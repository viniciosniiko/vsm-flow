<?php

namespace App\Http\Controllers;

use App\Models\Fluxo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConstrutorFluxoController extends Controller
{
    public function edit(Fluxo $fluxo)
    {
        $fluxo->load('cliente');

        return view('fluxos.construtor.edit', compact('fluxo'));
    }

    public function salvar(Request $request, Fluxo $fluxo): JsonResponse
    {
        $dados = $request->validate([
            'nodes' => ['required', 'array'],
            'connections' => ['required', 'array'],
        ]);

        $fluxo->update([
            'json_fluxo' => $dados,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Fluxo salvo com sucesso.',
            'saved_at' => now()->format('d/m/Y H:i:s'),
        ]);
    }
}

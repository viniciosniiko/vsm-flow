<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fluxo;
use Illuminate\Http\Request;

class FluxoController extends Controller
{
    public function create(Cliente $cliente)
    {
        return view('fluxos.create', compact('cliente'));
    }

    public function store(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => 'required|max:255',
            'descricao' => 'nullable',
            'status' => 'required|in:rascunho,aprovacao,aprovado,publicado',
        ]);

        $fluxo = $cliente->fluxos()->create([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'status' => $dados['status'],
            'versao' => 1,
            'json_fluxo' => [
                'blocos' => [],
                'conexoes' => [],
            ],
        ]);

        return redirect()
            ->route('fluxos.show', $fluxo)
            ->with('success', 'Fluxo criado com sucesso.');
    }

    public function show(Fluxo $fluxo)
    {
        $fluxo->load('cliente');

        return view('fluxos.show', compact('fluxo'));
    }

    public function edit(Fluxo $fluxo)
    {
        $fluxo->load('cliente');

        return view('fluxos.edit', compact('fluxo'));
    }

    public function update(Request $request, Fluxo $fluxo)
    {
        $dados = $request->validate([
            'nome' => 'required|max:255',
            'descricao' => 'nullable',
            'status' => 'required|in:rascunho,aprovacao,aprovado,publicado',
        ]);

        $fluxo->update($dados);

        return redirect()
            ->route('fluxos.show', $fluxo)
            ->with('success', 'Fluxo atualizado com sucesso.');
    }

    public function destroy(Fluxo $fluxo)
    {
        $cliente = $fluxo->cliente;

        $fluxo->delete();

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Fluxo removido.');
    }
}
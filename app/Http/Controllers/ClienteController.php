<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('fluxos')->orderBy('nome')->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|max:255',
            'empresa' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'status' => 'required|in:ativo,inativo',
        ]);

        $cliente = Cliente::create($dados);

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['fluxos' => function ($query) {
            $query->latest();
        }]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => 'required|max:255',
            'empresa' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'status' => 'required|in:ativo,inativo',
        ]);

        $cliente->update($dados);

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente removido.');
    }
}
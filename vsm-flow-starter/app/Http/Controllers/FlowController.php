<?php

namespace App\Http\Controllers;

use App\Models\Flow;
use Illuminate\Http\Request;

class FlowController extends Controller
{
    public function index()
    {
        return view('flows.index', [
            'flows' => Flow::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('flows.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'client_name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $data['status'] = 'draft';
        $flow = Flow::create($data);

        $flow->blocks()->create([
            'type' => 'message',
            'title' => 'Início',
            'message' => 'Olá! Seja bem-vindo. Como posso ajudar?',
            'position_x' => 80,
            'position_y' => 80,
            'is_start' => true,
        ]);

        return redirect()->route('flows.builder', $flow)->with('success', 'Fluxo criado com sucesso.');
    }

    public function edit(Flow $flow)
    {
        return view('flows.edit', compact('flow'));
    }

    public function update(Request $request, Flow $flow)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'client_name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,review,approved'],
        ]);

        $flow->update($data);

        return redirect()->route('flows.index')->with('success', 'Fluxo atualizado.');
    }

    public function destroy(Flow $flow)
    {
        $flow->delete();
        return redirect()->route('flows.index')->with('success', 'Fluxo removido.');
    }
}

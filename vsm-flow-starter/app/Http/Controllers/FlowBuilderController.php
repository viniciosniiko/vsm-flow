<?php

namespace App\Http\Controllers;

use App\Models\Flow;
use App\Models\FlowBlock;
use App\Models\FlowConnection;
use Illuminate\Http\Request;

class FlowBuilderController extends Controller
{
    public function edit(Flow $flow)
    {
        $flow->load(['blocks', 'connections']);
        return view('flows.builder', compact('flow'));
    }

    public function storeBlock(Request $request, Flow $flow)
    {
        $data = $request->validate([
            'type' => ['required', 'in:message,question,options,action'],
            'title' => ['required', 'string', 'max:150'],
            'message' => ['nullable', 'string'],
            'options_text' => ['nullable', 'string'],
        ]);

        $options = collect(preg_split('/\r\n|\r|\n/', $data['options_text'] ?? ''))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        $flow->blocks()->create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'] ?? null,
            'options' => $options,
            'position_x' => 120,
            'position_y' => 160 + ($flow->blocks()->count() * 80),
            'is_start' => false,
        ]);

        return back()->with('success', 'Bloco adicionado.');
    }

    public function updateBlock(Request $request, Flow $flow, FlowBlock $block)
    {
        abort_unless($block->flow_id === $flow->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'message' => ['nullable', 'string'],
            'options_text' => ['nullable', 'string'],
        ]);

        $options = collect(preg_split('/\r\n|\r|\n/', $data['options_text'] ?? ''))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();

        $block->update([
            'title' => $data['title'],
            'message' => $data['message'] ?? null,
            'options' => $options,
        ]);

        return back()->with('success', 'Bloco atualizado.');
    }

    public function destroyBlock(Flow $flow, FlowBlock $block)
    {
        abort_unless($block->flow_id === $flow->id, 404);
        $block->delete();
        return back()->with('success', 'Bloco removido.');
    }

    public function storeConnection(Request $request, Flow $flow)
    {
        $data = $request->validate([
            'from_block_id' => ['required', 'exists:flow_blocks,id'],
            'to_block_id' => ['required', 'exists:flow_blocks,id'],
            'condition_label' => ['nullable', 'string', 'max:150'],
        ]);

        FlowConnection::create([
            'flow_id' => $flow->id,
            'from_block_id' => $data['from_block_id'],
            'to_block_id' => $data['to_block_id'],
            'condition_label' => $data['condition_label'] ?? null,
        ]);

        return back()->with('success', 'Conexão criada.');
    }

    public function destroyConnection(Flow $flow, FlowConnection $connection)
    {
        abort_unless($connection->flow_id === $flow->id, 404);
        $connection->delete();
        return back()->with('success', 'Conexão removida.');
    }
}

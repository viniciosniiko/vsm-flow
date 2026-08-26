@extends('layouts.app')

@section('title', 'Builder - VSM Flow')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $flow->name }}</h1>
        <p>{{ $flow->client_name ?: 'Fluxo sem cliente informado' }}</p>
    </div>
    <a class="btn-soft" href="{{ route('flows.index') }}">Voltar</a>
</div>

<div class="builder-grid">
    <section class="builder-left">
        <div class="panel">
            <h2>Adicionar bloco</h2>
            <form class="form" method="POST" action="{{ route('flows.builder.block.store', $flow) }}">
                @csrf
                <label>Tipo</label>
                <select name="type">
                    <option value="message">Mensagem</option>
                    <option value="question">Pergunta</option>
                    <option value="options">Opções</option>
                    <option value="action">Ação</option>
                </select>

                <label>Título</label>
                <input name="title" required placeholder="Ex: Menu inicial">

                <label>Mensagem</label>
                <textarea name="message" placeholder="Texto que o bot enviará"></textarea>

                <label>Opções, uma por linha</label>
                <textarea name="options_text" placeholder="Agendar consulta&#10;Remarcar consulta&#10;Falar com atendente"></textarea>

                <button class="btn-primary" type="submit">Adicionar</button>
            </form>
        </div>

        <div class="panel">
            <h2>Criar conexão</h2>
            <form class="form" method="POST" action="{{ route('flows.builder.connection.store', $flow) }}">
                @csrf
                <label>De</label>
                <select name="from_block_id" required>
                    @foreach($flow->blocks as $block)
                        <option value="{{ $block->id }}">{{ $block->title }}</option>
                    @endforeach
                </select>

                <label>Para</label>
                <select name="to_block_id" required>
                    @foreach($flow->blocks as $block)
                        <option value="{{ $block->id }}">{{ $block->title }}</option>
                    @endforeach
                </select>

                <label>Condição/Opção</label>
                <input name="condition_label" placeholder="Ex: Agendar consulta">

                <button class="btn-primary" type="submit">Conectar</button>
            </form>
        </div>
    </section>

    <section class="flow-canvas">
        @foreach($flow->blocks as $block)
            <article class="flow-block type-{{ $block->type }}" data-block-id="{{ $block->id }}">
                <div class="block-head">
                    <span>{{ strtoupper($block->type) }}</span>
                    @if($block->is_start)<em>Início</em>@endif
                </div>
                <h3>{{ $block->title }}</h3>
                <p>{{ $block->message }}</p>

                @if(!empty($block->options))
                    <div class="option-list">
                        @foreach($block->options as $option)
                            <button type="button">{{ $option }}</button>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('flows.builder.block.destroy', [$flow, $block]) }}">
                    @csrf @method('DELETE')
                    <button class="link-danger" type="submit">Remover bloco</button>
                </form>
            </article>
        @endforeach

        <div class="connections-list">
            <h3>Conexões</h3>
            @foreach($flow->connections as $connection)
                @php
                    $from = $flow->blocks->firstWhere('id', $connection->from_block_id);
                    $to = $flow->blocks->firstWhere('id', $connection->to_block_id);
                @endphp
                <form class="connection-item" method="POST" action="{{ route('flows.builder.connection.destroy', [$flow, $connection]) }}">
                    @csrf @method('DELETE')
                    <span>{{ $from?->title }} → {{ $to?->title }} @if($connection->condition_label) / {{ $connection->condition_label }} @endif</span>
                    <button type="submit">x</button>
                </form>
            @endforeach
        </div>
    </section>

    <section class="phone-panel">
        <div class="phone">
            <div class="phone-header">WhatsApp · Simulação</div>
            <div id="chat-screen" class="chat-screen"></div>
            <div class="phone-footer">
                <button id="restart-simulation" type="button">Reiniciar simulação</button>
            </div>
        </div>
    </section>
</div>

<script>
window.VSM_FLOW_DATA = {
    blocks: @json($flow->blocks->values()),
    connections: @json($flow->connections->values())
};
</script>
@endsection

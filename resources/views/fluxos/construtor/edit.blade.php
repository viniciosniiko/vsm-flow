@extends('layouts.construtor')

@section('title', $fluxo->nome)

@section('content')
<div
    class="constructor-app"
    data-constructor
    data-flow-id="{{ $fluxo->id }}"
    data-save-url="{{ route('fluxos.construtor.salvar', $fluxo) }}"
    data-initial-flow='@json($fluxo->json_fluxo ?? ["nodes" => [], "connections" => []])'
>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <header class="constructor-topbar">
        <div class="constructor-breadcrumb">
            <a href="{{ route('clientes.show', $fluxo->cliente) }}" class="icon-button" title="Voltar">←</a>

            <div>
                <div class="eyebrow">{{ $fluxo->cliente->nome }}</div>
                <div class="constructor-title-row">
                    <h1>{{ $fluxo->nome }}</h1>
                    <span class="status-badge">{{ ucfirst($fluxo->status) }}</span>
                    <span class="version-badge">v{{ $fluxo->versao }}</span>
                </div>
            </div>
        </div>

        <div class="constructor-actions">
            <button type="button" class="button button-secondary" data-action="restart-simulation">
                Reiniciar teste
            </button>

            <button type="button" class="button button-primary" data-action="save">
                Salvar fluxo
            </button>
        </div>
    </header>

    <main class="constructor-workspace">
        <aside class="blocks-panel">
            <div class="panel-heading">
                <div>
                    <span class="eyebrow">Biblioteca</span>
                    <h2>Blocos</h2>
                </div>
            </div>

            <div class="blocks-search">
                <input type="search" placeholder="Pesquisar bloco..." data-block-search>
            </div>

            <div class="blocks-list">
                @php
                    $blocos = [
                        ['mensagem', '💬', 'Mensagem', 'Texto enviado pelo bot'],
                        ['opcoes', '🔘', 'Opções', 'Botões ou respostas rápidas'],
                        ['lista', '📋', 'Lista', 'Menu com várias opções'],
                        ['pergunta', '❓', 'Pergunta', 'Coleta uma resposta'],
                        ['condicao', '◇', 'Condição', 'Divide caminhos do fluxo'],
                        ['api', '🌐', 'API', 'Consulta ou envia dados'],
                        ['transferencia', '👤', 'Atendente', 'Transfere para humano'],
                        ['fim', '🏁', 'Finalizar', 'Encerra este caminho'],
                    ];
                @endphp

                @foreach($blocos as [$tipo, $icone, $titulo, $descricao])
                    <button class="block-item" type="button" data-block-type="{{ $tipo }}">
                        <span class="block-icon">{{ $icone }}</span>
                        <span>
                            <strong>{{ $titulo }}</strong>
                            <small>{{ $descricao }}</small>
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

        <section class="canvas-panel">
            <div class="canvas-toolbar">
                <div class="canvas-status">
                    <span class="status-dot"></span>
                    <span data-save-status>Alterações salvas</span>
                </div>

                <div class="canvas-help">
                    Arraste os blocos. Clique na saída roxa e depois no bloco de destino para conectar.
                </div>

                <div class="zoom-controls">
                    <button type="button" class="icon-button" data-action="zoom-out">−</button>
                    <span data-zoom-label>100%</span>
                    <button type="button" class="icon-button" data-action="zoom-in">+</button>
                    <button type="button" class="icon-button" data-action="center">⌖</button>
                </div>
            </div>

            <div class="canvas-area" data-canvas>
                <div class="canvas-grid"></div>

                <svg class="connections-layer" data-connections-layer></svg>

                <div class="canvas-empty" data-canvas-empty>
                    <div class="empty-orbit"><span>＋</span></div>
                    <h2>Comece o fluxo</h2>
                    <p>Clique em um bloco à esquerda para adicioná-lo ao canvas.</p>
                </div>

                <div class="flow-nodes" data-flow-nodes></div>
            </div>
        </section>

        <aside class="preview-panel">
            <div class="preview-panel-header">
                <div>
                    <span class="eyebrow">Tempo real</span>
                    <h2>Prévia da conversa</h2>
                </div>

                <span class="preview-live"><span></span> Ao vivo</span>
            </div>

            <div class="phone-shell">
                <div class="phone-notch"></div>

                <div class="phone-screen">
                    <div class="whatsapp-header">
                        <div class="whatsapp-back">‹</div>
                        <div class="whatsapp-avatar">V</div>
                        <div class="whatsapp-contact">
                            <strong>{{ $fluxo->cliente->nome }}</strong>
                            <small>online</small>
                        </div>
                        <div class="whatsapp-actions">⋮</div>
                    </div>

                    <div class="whatsapp-chat" data-preview-chat></div>

                    <form class="whatsapp-input" data-simulation-form>
                        <input
                            type="text"
                            placeholder="Digite uma mensagem"
                            autocomplete="off"
                            data-simulation-input
                        >
                        <button type="submit">➤</button>
                    </form>
                </div>
            </div>

            <div class="preview-tip">
                O teste percorre as conexões configuradas no canvas.
            </div>
        </aside>
    </main>

    <div class="property-backdrop" data-property-backdrop></div>

    <aside class="property-drawer" data-property-drawer>
        <div class="property-drawer-header">
            <div>
                <span class="eyebrow">Configuração do bloco</span>
                <h2 data-drawer-title>Mensagem</h2>
            </div>
            <button type="button" class="icon-button" data-action="close-properties">×</button>
        </div>

        <div class="property-drawer-body">
            <label class="field">
                <span>Título interno</span>
                <input type="text" data-property-title>
            </label>

            <label class="field">
                <span>Mensagem exibida no WhatsApp</span>
                <textarea rows="7" data-property-message></textarea>
            </label>

            <div class="options-editor is-hidden" data-options-editor>
                <div class="options-editor-header">
                    <div>
                        <strong>Opções</strong>
                        <small>Cada opção pode apontar para uma conexão futura.</small>
                    </div>
                    <button type="button" class="button button-secondary button-small" data-action="add-option">
                        + Opção
                    </button>
                </div>

                <div class="options-list" data-options-list></div>
            </div>

            <label class="field">
                <span>Observação interna</span>
                <textarea rows="3" data-property-note></textarea>
            </label>
        </div>

        <div class="property-drawer-footer">
            <button type="button" class="button button-danger-outline" data-action="delete-node">
                Excluir bloco
            </button>

            <button type="button" class="button button-primary" data-action="apply-properties">
                Aplicar alterações
            </button>
        </div>
    </aside>
</div>
@endsection

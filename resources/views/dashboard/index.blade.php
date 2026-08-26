@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Visão geral dos fluxos de chatbot')

@section('content')

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card-flow">
            <div class="muted">Clientes</div>
            <div class="metric">{{ $totalClientes }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-flow">
            <div class="muted">Fluxos</div>
            <div class="metric">{{ $totalFluxos }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-flow">
            <div class="muted">Rascunhos</div>
            <div class="metric">{{ $fluxosRascunho }}</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-flow">
            <div class="muted">Aprovados</div>
            <div class="metric">{{ $fluxosAprovados }}</div>
        </div>
    </div>
</div>

<div class="card-flow">
    <h4 class="mb-3">Últimos fluxos</h4>

    @if($fluxos->count())
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fluxo</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Versão</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fluxos as $fluxo)
                        <tr>
                            <td>{{ $fluxo->nome }}</td>
                            <td>{{ $fluxo->cliente->nome ?? '-' }}</td>
                            <td>{{ ucfirst($fluxo->status) }}</td>
                            <td>v{{ $fluxo->versao }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="muted mb-0">
            Nenhum fluxo cadastrado ainda. Em breve criaremos o primeiro.
        </p>
    @endif
</div>

@endsection
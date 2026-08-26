@extends('layouts.app')

@section('title', $fluxo->nome)
@section('subtitle', 'Fluxo de ' . $fluxo->cliente->nome)

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-flow mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h3>{{ $fluxo->nome }}</h3>
            <p class="muted mb-3">{{ $fluxo->descricao ?? 'Sem descrição' }}</p>

            <span class="badge bg-primary">v{{ $fluxo->versao }}</span>
            <span class="badge bg-secondary">{{ ucfirst($fluxo->status) }}</span>
        </div>

        <div>
			<a href="{{ route('fluxos.construtor', $fluxo) }}" class="btn-vsm">
				<i class="fa-solid fa-diagram-project"></i> Abrir construtor
			</a>

            <a href="{{ route('clientes.show', $fluxo->cliente) }}" class="btn btn-outline-light ms-2">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card-flow">
            <div class="muted">Blocos</div>
            <div class="metric">0</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-flow">
            <div class="muted">Conexões</div>
            <div class="metric">0</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-flow">
            <div class="muted">Homologação</div>
            <div class="metric">-</div>
        </div>
    </div>
</div>

@endsection
@extends('layouts.app')

@section('title', 'Dashboard - VSM Flow')

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Criação, validação e simulação de fluxos de WhatsApp Oficial.</p>
    </div>
    <a class="btn-primary" href="{{ route('flows.create') }}">Novo fluxo</a>
</div>

<div class="cards">
    <div class="card"><strong>{{ $totalFlows }}</strong><span>Total de fluxos</span></div>
    <div class="card"><strong>{{ $draftFlows }}</strong><span>Em rascunho</span></div>
    <div class="card"><strong>{{ $approvedFlows }}</strong><span>Aprovados</span></div>
</div>

<div class="panel">
    <h2>Últimos fluxos</h2>
    @foreach($flows as $flow)
        <div class="row-item">
            <div>
                <strong>{{ $flow->name }}</strong>
                <small>{{ $flow->client_name ?: 'Sem cliente informado' }}</small>
            </div>
            <a href="{{ route('flows.builder', $flow) }}">Abrir</a>
        </div>
    @endforeach
</div>
@endsection

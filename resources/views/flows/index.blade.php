@extends('layouts.app')

@section('title', 'Fluxos - VSM Flow')

@section('content')
<div class="page-header">
    <div>
        <h1>Fluxos</h1>
        <p>Gerencie os fluxos dos clientes.</p>
    </div>
    <a class="btn-primary" href="{{ route('flows.create') }}">Novo fluxo</a>
</div>

<div class="panel">
    @foreach($flows as $flow)
        <div class="row-item">
            <div>
                <strong>{{ $flow->name }}</strong>
                <small>{{ $flow->client_name ?: 'Sem cliente' }} · {{ $flow->status }}</small>
            </div>
            <div class="actions">
                <a href="{{ route('flows.builder', $flow) }}">Builder</a>
                <a href="{{ route('flows.edit', $flow) }}">Editar</a>
                <form method="POST" action="{{ route('flows.destroy', $flow) }}">
                    @csrf @method('DELETE')
                    <button type="submit">Excluir</button>
                </form>
            </div>
        </div>
    @endforeach

    {{ $flows->links() }}
</div>
@endsection

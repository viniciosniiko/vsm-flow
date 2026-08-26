@extends('layouts.app')

@section('title', $cliente->nome)
@section('subtitle', 'Workspace do cliente')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card-flow">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h3 class="mb-1">{{ $cliente->nome }}</h3>
                    <div class="muted">{{ $cliente->empresa ?? 'Empresa não informada' }}</div>
                </div>

                <span class="badge bg-{{ $cliente->status === 'ativo' ? 'success' : 'secondary' }}">
                    {{ ucfirst($cliente->status) }}
                </span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="muted">Email</div>
                    <strong>{{ $cliente->email ?? '-' }}</strong>
                </div>

                <div class="col-md-3">
                    <div class="muted">Telefone</div>
                    <strong>{{ $cliente->telefone ?? '-' }}</strong>
                </div>

                <div class="col-md-3">
                    <div class="muted">WhatsApp</div>
                    <strong>{{ $cliente->whatsapp ?? '-' }}</strong>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-outline-light">
                    <i class="fa-solid fa-pen"></i> Editar dados
                </a>

                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary ms-2">
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-flow">
            <div class="muted">Total de fluxos</div>
            <div class="metric">{{ $cliente->fluxos->count() }}</div>
        </div>
    </div>
</div>

<div class="card-flow">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Fluxos do cliente</h4>
            <div class="muted">Chatbots, jornadas e atendimentos vinculados a este cliente</div>
        </div>

		<a href="{{ route('clientes.fluxos.create', $cliente) }}" class="btn-vsm">
			<i class="fa-solid fa-plus"></i> Novo fluxo
		</a>
    </div>

    @if($cliente->fluxos->count())
        <div class="row g-4">
		@foreach($cliente->fluxos as $fluxo)
			<div class="col-md-4">
				<div class="card-flow">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<h5>{{ $fluxo->nome }}</h5>
							<div class="muted">{{ $fluxo->descricao ?? 'Sem descrição' }}</div>
						</div>

						<span class="badge bg-primary">
							v{{ $fluxo->versao }}
						</span>
					</div>

					<div class="mt-3">
						<span class="badge bg-secondary">
							{{ ucfirst($fluxo->status) }}
						</span>
					</div>

					<a href="{{ route('fluxos.show', $fluxo) }}" class="btn btn-sm btn-outline-light mt-3">
						Abrir fluxo
					</a>
				</div>
			</div>
		@endforeach
        </div>
    @else
        <p class="muted mb-0">
            Nenhum fluxo criado para este cliente ainda.
        </p>
    @endif
</div>

@endsection
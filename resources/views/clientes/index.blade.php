@extends('layouts.app')

@section('title', 'Clientes')
@section('subtitle', 'Empresas e clientes com fluxos de chatbot')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card-flow">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Lista de clientes</h4>

        <a href="{{ route('clientes.create') }}" class="btn-vsm">
            <i class="fa-solid fa-plus"></i> Novo cliente
        </a>
    </div>

    @if($clientes->count())
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Empresa</th>
                        <th>Email</th>
                        <th>WhatsApp</th>
                        <th>Status</th>
                        <th width="160">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->nome }}</td>
                            <td>{{ $cliente->empresa ?? '-' }}</td>
                            <td>{{ $cliente->email ?? '-' }}</td>
                            <td>{{ $cliente->whatsapp ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $cliente->status === 'ativo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($cliente->status) }}
                                </span>
                            </td>
                            <td>
								<a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-outline-light">
									Abrir
								</a>

                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Deseja remover este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $clientes->links() }}
    @else
        <p class="muted mb-0">Nenhum cliente cadastrado ainda.</p>
    @endif
</div>

@endsection
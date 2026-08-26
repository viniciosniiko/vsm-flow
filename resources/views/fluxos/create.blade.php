@extends('layouts.app')

@section('title', 'Novo fluxo')
@section('subtitle', 'Criar fluxo para ' . $cliente->nome)

@section('content')

<div class="card-flow">
    <form action="{{ route('clientes.fluxos.store', $cliente) }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Nome do fluxo *</label>
                <input type="text" name="nome" class="form-control" required
                       placeholder="Ex: Agendamento, Financeiro, Exames">
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="rascunho">Rascunho</option>
                    <option value="aprovacao">Em aprovação</option>
                    <option value="aprovado">Aprovado</option>
                    <option value="publicado">Publicado</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="4"
                          placeholder="Descreva o objetivo desse fluxo..."></textarea>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn-vsm">
                <i class="fa-solid fa-plus"></i> Criar fluxo
            </button>

            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-light ms-2">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection
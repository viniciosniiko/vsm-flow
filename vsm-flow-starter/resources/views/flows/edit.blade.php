@extends('layouts.app')

@section('title', 'Editar fluxo - VSM Flow')

@section('content')
<div class="page-header"><h1>Editar fluxo</h1></div>

<form class="panel form" method="POST" action="{{ route('flows.update', $flow) }}">
    @csrf @method('PUT')
    <label>Nome do fluxo</label>
    <input name="name" required value="{{ old('name', $flow->name) }}">

    <label>Cliente</label>
    <input name="client_name" value="{{ old('client_name', $flow->client_name) }}">

    <label>Status</label>
    <select name="status">
        <option value="draft" @selected($flow->status === 'draft')>Rascunho</option>
        <option value="review" @selected($flow->status === 'review')>Em aprovação</option>
        <option value="approved" @selected($flow->status === 'approved')>Aprovado</option>
    </select>

    <label>Descrição</label>
    <textarea name="description">{{ old('description', $flow->description) }}</textarea>

    <button class="btn-primary" type="submit">Salvar</button>
</form>
@endsection

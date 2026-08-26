@extends('layouts.app')

@section('title', 'Novo fluxo - VSM Flow')

@section('content')
<div class="page-header"><h1>Novo fluxo</h1></div>

<form class="panel form" method="POST" action="{{ route('flows.store') }}">
    @csrf
    <label>Nome do fluxo</label>
    <input name="name" required placeholder="Ex: Agendamento Clínica Modelo">

    <label>Cliente</label>
    <input name="client_name" placeholder="Ex: Clínica Modelo">

    <label>Descrição</label>
    <textarea name="description" placeholder="Objetivo do fluxo"></textarea>

    <button class="btn-primary" type="submit">Criar fluxo</button>
</form>
@endsection

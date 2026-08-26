@extends('layouts.app')

@section('title', 'Novo cliente')
@section('subtitle', 'Cadastre um cliente para criar fluxos')

@section('content')

<div class="card-flow">
    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        @include('clientes._form')

        <div class="mt-4">
            <button class="btn-vsm">Salvar cliente</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-light ms-2">Cancelar</a>
        </div>
    </form>
</div>

@endsection
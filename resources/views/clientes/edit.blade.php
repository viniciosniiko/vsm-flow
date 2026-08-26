@extends('layouts.app')

@section('title', 'Editar cliente')
@section('subtitle', $cliente->nome)

@section('content')

<div class="card-flow">
    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')

        @include('clientes._form')

        <div class="mt-4">
            <button class="btn-vsm">Salvar alterações</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-light ms-2">Cancelar</a>
        </div>
    </form>
</div>

@endsection
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'VSM Flow')</title>
    <link rel="stylesheet" href="{{ asset('css/vsm-flow.css') }}">
</head>
<body>
    <aside class="sidebar">
        <div class="brand">VSM <span>Flow</span></div>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('flows.index') }}">Fluxos</a>
        <a href="{{ route('flows.create') }}">Novo fluxo</a>
    </aside>

    <main class="main">
        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <script src="{{ asset('js/vsm-flow.js') }}"></script>
    @stack('scripts')
</body>
</html>

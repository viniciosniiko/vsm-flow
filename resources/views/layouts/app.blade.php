<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSM Flow</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #0f172a;
            color: #e5e7eb;
            font-family: Inter, Arial, sans-serif;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #020617;
            border-right: 1px solid #1e293b;
            padding: 24px;
        }

        .brand {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 32px;
        }

        .brand span {
            color: #8b5cf6;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .menu a:hover,
        .menu a.active {
            background: #1e293b;
            color: #fff;
        }

        .main {
            flex: 1;
            padding: 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .card-flow {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 24px;
        }

        .metric {
            font-size: 32px;
            font-weight: 800;
        }

        .muted {
            color: #94a3b8;
        }

        .btn-vsm {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            text-decoration: none;
        }

        .btn-vsm:hover {
            color: white;
            opacity: .9;
        }
    </style>
</head>
<body>

<div class="app">
    <aside class="sidebar">
        <div class="brand">
            VSM <span>Flow</span>
        </div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}" class="active">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>

			<a href="{{ route('clientes.index') }}" class="{{ request()->routeIs('clientes.*') ? 'active' : '' }}">
				<i class="fa-solid fa-building"></i> Clientes
			</a>

            <a href="#">
                <i class="fa-solid fa-diagram-project"></i> Fluxos
            </a>

            <a href="#">
                <i class="fa-solid fa-layer-group"></i> Templates
            </a>

            <a href="#">
                <i class="fa-brands fa-whatsapp"></i> Simulações
            </a>

            <a href="#">
                <i class="fa-solid fa-circle-check"></i> Aprovações
            </a>

            <a href="#">
                <i class="fa-solid fa-gear"></i> Configurações
            </a>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="mb-1">@yield('title', 'Dashboard')</h1>
                <div class="muted">@yield('subtitle', 'Painel de controle do VSM Flow')</div>
            </div>

            <a href="#" class="btn-vsm">
                <i class="fa-solid fa-plus"></i> Novo fluxo
            </a>
        </div>

        @yield('content')
    </main>
</div>

</body>
</html>
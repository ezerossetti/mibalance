<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MiBalance') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { overflow-x: hidden; }
        #sidebar-wrapper { min-height: 100vh; margin-left: -15rem; transition: margin 0.25s ease-out; }
        #sidebar-wrapper .sidebar-heading { padding: 0.875rem 1.25rem; font-size: 1.2rem; }
        #sidebar-wrapper .list-group { width: 15rem; }
        #page-content-wrapper { min-width: 100vw; }
        body.sb-sidenav-toggled #sidebar-wrapper { margin-left: 0; }
        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { min-width: 0; width: 100%; }
            body.sb-sidenav-toggled #sidebar-wrapper { margin-left: -15rem; }
        }
        .sidebar-item { color: rgba(255, 255, 255, 0.7); }
        .sidebar-item:hover, .sidebar-item.active { color: #fff; background-color: #495057; }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-dark border-right d-flex flex-column" id="sidebar-wrapper">
            <div class="sidebar-heading text-light">MiBalance</div>
            <div class="list-group list-group-flush flex-grow-1">
                <a href="{{ url('/home') }}" class="list-group-item list-group-item-action bg-dark sidebar-item {{ request()->is('home') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a href="{{ route('categorias.index') }}" class="list-group-item list-group-item-action bg-dark sidebar-item {{ request()->is('categorias*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill me-2"></i>Categorías
                </a>
                <a href="{{ route('formaspago.index') }}" class="list-group-item list-group-item-action bg-dark sidebar-item {{ request()->is('formaspago*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-fill me-2"></i>Formas de Pago
                </a>
                <a href="#transaccionesSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="list-group-item list-group-item-action bg-dark sidebar-item d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-arrow-down-up me-2"></i>Transacciones</div>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse" id="transaccionesSubmenu">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('transaccions.index') }}" class="list-group-item list-group-item-action bg-dark sidebar-item ps-5">Ver Todas</a>
                        <a href="{{ route('transaccions.create') }}" class="list-group-item list-group-item-action bg-dark sidebar-item ps-5">Nueva Transacción</a>
                    </div>
                </div>
                <a href="{{ route('reportes.index') }}" class="list-group-item list-group-item-action bg-dark sidebar-item {{ request()->is('reportes*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Reportes
                </a>

                <div class="mt-auto">
                    <a href="{{ route('perfil.edit') }}" class="list-group-item list-group-item-action bg-dark sidebar-item {{ request()->is('perfil*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle me-2"></i>Mi Perfil
                    </a>
                    <a href="{{ route('logout') }}" class="list-group-item list-group-item-action bg-dark sidebar-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-primary d-md-none" id="menu-toggle"><i class="bi bi-list"></i></button>
                </div>
            </nav>
            <main class="container-fluid py-4">
                @yield('content')
            </main>
        </div>
        </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const menuToggle = document.body.querySelector('#menu-toggle');
            if (menuToggle) {
                menuToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });
        </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>

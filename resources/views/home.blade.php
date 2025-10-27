@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Muestra mensaje de éxito si existe en la sesión --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- MUESTRA MENSAJE DE ALERTA (WARNING) SI EXISTE --}}
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{-- Ícono de advertencia --}}
        <strong>{{ session('warning') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Título y Botón Rápido --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Bienvenido de Nuevo, {{ Auth::user()->nombre }} !
                </h2>
                <a href="{{ route('transaccions.create') }}" class="btn btn-primary btn-lg d-none d-md-inline-flex">
                    <i class="bi bi-plus-circle-fill me-2"></i>Nueva Transacción
                </a>
            </div>

            {{-- Fila de Tarjetas de Resumen --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h3 class="card-title" style="font-size: 1.75rem; font-weight: bold;">$ {{ number_format($ingresosMes, 2, ',', '.') }}</h3>
                            <p class="card-text">Ingresos del Mes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h3 class="card-title" style="font-size: 1.75rem; font-weight: bold;">$ {{ number_format($gastosMes, 2, ',', '.') }}</h3>
                            <p class="card-text">Gastos del Mes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h3 class="card-title" style="font-size: 1.75rem; font-weight: bold;">$ {{ number_format($saldoTotal, 2, ',', '.') }}</h3>
                            <p class="card-text">Saldo Total (Histórico)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fila para Gráfico de Torta, Top Categorías y Últimas Transacciones --}}
            <div class="row mt-4">
                {{-- Columna para el Gráfico de Torta y Top Categorías --}}
                <div class="col-lg-5 mb-4">
                    <div class="card h-100">
                        <div class="card-header">Gastos por Categoría (Mes Actual)</div>
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            @if($dataGraficoTorta->isNotEmpty())
                                <div style="max-height: 250px; width: 100%; margin-bottom: 1rem;">
                                    <canvas id="gastosChart"></canvas>
                                </div>
                                <h6 class="mt-3">Top 5 Categorías de Gasto:</h6>
                                <ul class="list-group list-group-flush w-100 text-center">
                                    @forelse($topCategoriasGasto as $topCat)
                                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-1">
                                            {{ $topCat->nombre }}
                                            <span class="badge bg-danger rounded-pill">$ {{ number_format($topCat->total, 2, ',', '.') }}</span>
                                        </li>
                                    @empty
                                         <li class="list-group-item border-0 px-0 py-1 text-muted">No hay gastos este mes.</li>
                                    @endforelse
                                </ul>
                            @else
                                <p class="text-muted mb-0">No hay gastos para mostrar en el gráfico.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Columna para la Tabla de Últimas Transacciones --}}
                <div class="col-lg-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Transacciones del Mes Actual</span>
                            <a href="{{ route('transaccions.index') }}" class="btn btn-outline-primary btn-sm">Ver Todas</a>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @forelse ($transaccionesRecientes as $transaccion)
                                        <tr>
                                            <td>{{ \Illuminate\Support\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $transaccion->categoria->nombre }}</td>
                                            <td>
                                                @if ($transaccion->categoria->tipo_categoria == 'Ingreso')
                                                    <span class="badge bg-success-subtle text-success-emphasis rounded-pill">$ {{ number_format($transaccion->monto, 2, ',', '.') }}</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">$ -{{ number_format($transaccion->monto, 2, ',', '.') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">No hay transacciones este mes.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> {{-- Cierre Fila Gráfico Torta y Tabla --}}

            {{-- Fila para Gráfico de Tendencia --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                         <div class="card-header">Tendencia Últimos 6 Meses (Ingresos vs. Gastos)</div>
                         <div class="card-body" style="height: 300px;">
                             <canvas id="tendenciaChart"></canvas>
                         </div>
                    </div>
                </div>
            </div>

        </div> {{-- Cierre col-md-12 --}}
    </div> {{-- Cierre row justify-content-center --}}
</div> {{-- Cierre container --}}

{{-- Scripts para inicializar los gráficos --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Gráfico de Torta (Gastos por Categoría)
        const canvasTorta = document.getElementById('gastosChart');
        if (canvasTorta && @json($dataGraficoTorta->isNotEmpty())) {
            new Chart(canvasTorta.getContext('2d'), {
                type: 'doughnut', data: {
                    labels: {!! json_encode($labelsGraficoTorta) !!},
                    datasets: [{
                        data: {!! json_encode($dataGraficoTorta) !!},
                        backgroundColor: ['rgba(255, 99, 132, 0.8)','rgba(54, 162, 235, 0.8)','rgba(255, 206, 86, 0.8)','rgba(75, 192, 192, 0.8)','rgba(153, 102, 255, 0.8)','rgba(255, 159, 64, 0.8)'],
                        borderColor: '#fff', borderWidth: 2
                    }]
                }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // Gráfico de Barras (Tendencia Mensual)
        const canvasBarras = document.getElementById('tendenciaChart');
        if (canvasBarras) {
            new Chart(canvasBarras.getContext('2d'), {
                type: 'bar', data: {
                    labels: {!! json_encode($mesesTendencia) !!},
                    datasets: [
                        { label: 'Ingresos', data: {!! json_encode($ingresosTendencia) !!}, backgroundColor: 'rgba(75, 192, 192, 0.7)' },
                        { label: 'Gastos', data: {!! json_encode($gastosTendencia) !!}, backgroundColor: 'rgba(255, 99, 132, 0.7)' }
                    ]
                }, options: {
                    responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: value => '$ ' + value.toLocaleString('es-AR') } } },
                    plugins: { tooltip: { callbacks: { label: context => context.dataset.label + ': $ ' + context.parsed.y.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } } }
                }
            });
        }
    });
</script>
@endpush
@endsection

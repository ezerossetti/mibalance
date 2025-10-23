@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        Reporte de Resumen Mensual
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('reportes.index') }}" class="row g-3 mb-4 align-items-center justify-content-end">
            <div class="col-auto">
                <label for="year" class="col-form-label">Seleccionar Año:</label>
            </div>
            <div class="col-auto">
                <select name="year" id="year" class="form-select">
                    @forelse ($yearsWithTransactions as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @empty
                        <option>{{ $year }}</option>
                    @endforelse
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ver Reporte</button>
            </div>
        </form>

        <div class="mb-5" style="height: 300px;">
            <canvas id="mensualBarChart"></canvas>
        </div>

        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>Mes</th>
                    <th>Ingresos</th>
                    <th>Gastos</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reporte as $resumen)
                    <tr>
                        <td>{{ $resumen['nombreMes'] }}</td>
                        <td class="text-success">${{ number_format($resumen['ingresos'], 2, ',', '.') }}</td>
                        <td class="text-danger">${{ number_format($resumen['gastos'], 2, ',', '.') }}</td>
                        <td class="fw-bold @if($resumen['balance'] < 0) text-danger @else text-primary @endif">
                            ${{ number_format($resumen['balance'], 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctxBar = document.getElementById('mensualBarChart');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($mesesGrafico) !!},
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: {!! json_encode($ingresosGrafico) !!},
                            backgroundColor: 'rgba(75, 192, 192, 0.7)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Gastos',
                            data: {!! json_encode($gastosGrafico) !!},
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return '$ ' + value.toLocaleString('es-AR');
                                }
                            }
                        }
                    },
                    plugins: {
                       tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += '$ ' + context.parsed.y.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

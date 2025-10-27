@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Mis Alertas de Gasto</span>
        {{-- El botón para crear lo haremos apuntar a la ruta correcta después --}}
        <a href="{{ route('alertas.create') }}" class="btn btn-primary btn-sm">
            + Crear Nueva Alerta
        </a>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <p class="text-muted">Aquí podés definir límites de gasto mensual por categoría. El sistema te avisará (próximamente) si te pasás.</p>

        <table class="table table-striped table-hover mt-3">
            <thead>
                <tr>
                    <th scope="col">Categoría</th>
                    <th scope="col">Condición</th>
                    <th scope="col">Límite</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- Recorremos las alertas obtenidas del controlador --}}
                @forelse ($alertas as $alerta)
                    <tr>
                        {{-- Mostramos el nombre de la categoría relacionada --}}
                        <td>{{ $alerta->categoria->nombre ?? 'Categoría eliminada' }}</td>
                        <td>Gasto mensual mayor a</td>
                        {{-- Mostramos el límite formateado --}}
                        <td>${{ number_format($alerta->limite, 2, ',', '.') }}</td>
                        {{-- Mostramos si está activa o no --}}
                        <td>
                            @if($alerta->activa)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-secondary">Inactiva</span>
                            @endif
                        </td>
                        <td>
    {{-- Botón Editar --}}
    <a href="{{ route('alertas.edit', $alerta->id) }}" class="btn btn-warning btn-sm">Editar</a>
    {{-- Botón Borrar --}}
    <form action="{{ route('alertas.destroy', $alerta->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Borrar</button>
    </form>
</td>
                    </tr>
                {{-- Se muestra si no hay alertas definidas --}}
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No has definido ninguna alerta.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

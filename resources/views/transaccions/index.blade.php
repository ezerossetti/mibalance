@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Mis Transacciones</span>
        <div>
            <a href="{{ route('transaccions.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>
            <a href="{{ route('transaccions.export.csv', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> CSV
            </a>
            <a href="{{ route('transaccions.create') }}" class="btn btn-primary btn-sm ms-2">
                + Nueva Transacción
            </a>
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('transaccions.index') }}" class="row g-3 mb-4 align-items-center">
            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="Ingreso" {{ request('tipo') == 'Ingreso' ? 'selected' : '' }}>Ingreso</option>
                    <option value="Gasto" {{ request('tipo') == 'Gasto' ? 'selected' : '' }}>Gasto</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categorias_filtro as $categoria)
                        <option value="{{ $categoria->idcategoria }}" {{ request('categoria_id') == $categoria->idcategoria ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="fecha_desde" class="form-label">Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <label for="fecha_hasta" class="form-label">Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('transaccions.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Monto</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaccions as $transaccion)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}</td>
                        <td>
                            @if ($transaccion->categoria->tipo_categoria == 'Ingreso')
                                <span class="text-success fw-bold">$ {{ number_format($transaccion->monto, 2, ',', '.') }}</span>
                            @else
                                <span class="text-danger fw-bold">$ -{{ number_format($transaccion->monto, 2, ',', '.') }}</span>
                            @endif
                        </td>
                        <td>{{ $transaccion->categoria->nombre }}</td>
                        <td>
                            {{ $transaccion->descripcion }}
                            @if ($transaccion->alias_destinatario)
                                <small class="d-block text-muted">
                                    Dest.: {{ $transaccion->nombre_destinatario }} ({{ $transaccion->alias_destinatario }})
                                </small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('transaccions.edit', $transaccion->idtransaccion) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('transaccions.destroy', $transaccion->idtransaccion) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que querés borrar esta transacción?')">Borrar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay transacciones registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

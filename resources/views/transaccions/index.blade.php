@extends('layouts.app')

@section('content')
<div class="card">
    {{-- Cabecera con título y botones de acción --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Mis Transacciones</span>
        <div>
            {{-- Botón Exportar PDF (incluye los parámetros de filtro actuales) --}}
            <a href="{{ route('transaccions.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>
            {{-- Botón Exportar CSV (incluye los parámetros de filtro actuales) --}}
            <a href="{{ route('transaccions.export.csv', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> CSV
            </a>
            {{-- Botón Nueva Transacción --}}
            <a href="{{ route('transaccions.create') }}" class="btn btn-primary btn-sm ms-2">
                + Nueva Transacción
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- Formulario de Filtros --}}
        <form method="GET" action="{{ route('transaccions.index') }}" class="row g-3 mb-4 align-items-center">
            {{-- Filtro por Tipo --}}
            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="Ingreso" {{ request('tipo') == 'Ingreso' ? 'selected' : '' }}>Ingreso</option>
                    <option value="Gasto" {{ request('tipo') == 'Gasto' ? 'selected' : '' }}>Gasto</option>
                </select>
            </div>
            {{-- Filtro por Categoría --}}
            <div class="col-md-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="form-select">
                    <option value="">Todas</option>
                    {{-- Recorre las categorías enviadas por el controlador --}}
                    @foreach($categorias_filtro as $categoria)
                        <option value="{{ $categoria->idcategoria }}" {{ request('categoria_id') == $categoria->idcategoria ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Filtro por Fecha Desde --}}
            <div class="col-md-3">
                <label for="fecha_desde" class="form-label">Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            {{-- Filtro por Fecha Hasta --}}
            <div class="col-md-3">
                <label for="fecha_hasta" class="form-label">Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            {{-- Botones del filtro --}}
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('transaccions.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

        {{-- Muestra mensaje de éxito si existe en la sesión --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tabla de Transacciones --}}
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
                {{-- Bucle para mostrar cada transacción --}}
                @forelse ($transaccions as $transaccion)
                    <tr>
                        {{-- Muestra la fecha formateada --}}
                        <td>{{ \Illuminate\Support\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}</td>
                        {{-- Muestra el monto con color según el tipo --}}
                        <td>
                            @if ($transaccion->categoria->tipo_categoria == 'Ingreso')
                                <span class="text-success fw-bold">$ {{ number_format($transaccion->monto, 2, ',', '.') }}</span>
                            @else
                                <span class="text-danger fw-bold">$ -{{ number_format($transaccion->monto, 2, ',', '.') }}</span>
                            @endif
                        </td>
                        {{-- Muestra el nombre de la categoría (usando la relación) --}}
                        {{-- El ?? 'N/A' es por si se borra una categoría asociada --}}
                        <td>{{ $transaccion->categoria->nombre ?? 'N/A' }}</td>
                        {{-- Muestra la descripción y detalles adicionales (transferencia, cuotas) --}}
                        <td>
                            {{ $transaccion->descripcion }}
                            {{-- Muestra info de transferencia si existe --}}
                            @if ($transaccion->alias_destinatario)
                                <small class="d-block text-muted fst-italic">
                                    Dest.: {{ $transaccion->nombre_destinatario }} ({{ $transaccion->alias_destinatario }})
                                </small>
                            @endif
                            {{-- Muestra info de cuotas si existe --}}
                            @if ($transaccion->cuotas && $transaccion->cuotas > 1)
                                <small class="d-block text-muted fst-italic">En {{ $transaccion->cuotas }} cuotas</small>
                            @elseif ($transaccion->cuotas === 1) {{-- Puedes quitar este elseif si no quieres mostrar "En 1 cuota" --}}
                                 <small class="d-block text-muted fst-italic">En 1 cuota</small>
                            @endif
                        </td>
                        {{-- Botones de acción (Editar y Borrar) --}}
                        <td>
                            <a href="{{ route('transaccions.edit', $transaccion->idtransaccion) }}" class="btn btn-warning btn-sm" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('transaccions.destroy', $transaccion->idtransaccion) }}" method="POST" class="d-inline" title="Borrar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        {{-- Mensaje de confirmación dinámico si es una cuota --}}
                                        onclick="return confirm('{{ $transaccion->grupo_compra_id ? 'Esto borrará TODAS las cuotas (' . $transaccion->cuotas . ') de esta compra. ' : '' }}¿Estás seguro de que querés borrar esta transacción?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                {{-- Se muestra si no hay transacciones (después de aplicar filtros o si está vacío) --}}
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay transacciones registradas que coincidan con los filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Aquí podríamos agregar paginación si hubiera muchas transacciones --}}
        {{-- {{ $transaccions->links() }} --}}

    </div> {{-- Fin card-body --}}
</div> {{-- Fin card --}}
@endsection

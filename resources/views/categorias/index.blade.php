@extends('layouts.app')

@section('content')
<div class="card">
    {{-- EN LA CABECERA SÓLO VA EL BOTÓN GENERAL DE "CREAR" --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Mis Categorías</span>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-sm">
            + Crear Nueva Categoría
        </a>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr>
                        <td>{{ $categoria->nombre }}</td>
                        <td>{{ $categoria->descripcion }}</td>
                        <td>{{ $categoria->tipo_categoria }}</td>
                        {{-- AQUÍ VAN LOS BOTONES DE ACCIÓN PARA CADA FILA --}}
                        <td>
                            <a href="{{ route('categorias.edit', $categoria->idcategoria) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('categorias.destroy', $categoria->idcategoria) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Borrar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay categorías registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

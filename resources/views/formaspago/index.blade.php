@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Formas de Pago</span>
        <a href="{{ route('formaspago.create') }}" class="btn btn-primary btn-sm">
            + Agregar Nueva
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
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($formaspago as $forma)
                    <tr>
                        <th scope="row">{{ $forma->idforma_pago }}</th>
                        <td>{{ $forma->nombre }}</td>
                        <td>
                            <a href="{{ route('formaspago.edit', $forma->idforma_pago) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('formaspago.destroy', $forma->idforma_pago) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?')">Borrar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No hay formas de pago registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

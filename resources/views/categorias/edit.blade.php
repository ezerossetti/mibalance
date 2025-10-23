@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Categoría') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('categorias.update', $categoria->idcategoria) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $categoria->nombre }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ $categoria->descripcion }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_categoria" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo_categoria" name="tipo_categoria" required>
                                <option value="Ingreso" {{ $categoria->tipo_categoria == 'Ingreso' ? 'selected' : '' }}>Ingreso</option>
                                <option value="Gasto" {{ $categoria->tipo_categoria == 'Gasto' ? 'selected' : '' }}>Gasto</option>
                                <option value="Ambos" {{ $categoria->tipo_categoria == 'Ambos' ? 'selected' : '' }}>Ambos</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

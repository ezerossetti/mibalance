@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Crear Nueva Categoría') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('categorias.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_categoria" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo_categoria" name="tipo_categoria" required>
                                <option value="Ingreso">Ingreso</option>
                                <option value="Gasto">Gasto</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Crear Nueva Alerta de Gasto') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('alertas.store') }}">
                        @csrf

                        {{-- Categoría --}}
                        <div class="mb-3">
                            <label for="idcategoria" class="form-label">Categoría de Gasto</label>
                            <select class="form-select @error('idcategoria') is-invalid @enderror" id="idcategoria" name="idcategoria" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categoriasGasto as $categoria)
                                    <option value="{{ $categoria->idcategoria }}" {{ old('idcategoria') == $categoria->idcategoria ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idcategoria')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        {{-- Límite --}}
                        <div class="mb-3">
                            <label for="limite" class="form-label">Avisarme si el gasto mensual supera</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control @error('limite') is-invalid @enderror" id="limite" name="limite" value="{{ old('limite') }}" required min="0.01">
                            </div>
                            @error('limite')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Alerta</button>
                        <a href="{{ route('alertas.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

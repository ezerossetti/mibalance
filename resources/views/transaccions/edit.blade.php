@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Transacción') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('transaccions.update', $transaccion->idtransaccion) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="monto" name="monto" value="{{ $transaccion->monto }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $transaccion->fecha }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="idcategoria" class="form-label">Categoría</label>
                            <select class="form-select" id="idcategoria" name="idcategoria" required>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->idcategoria }}" {{ $transaccion->idcategoria == $categoria->idcategoria ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="idforma_pago" class="form-label">Forma de Pago</label>
                            <select class="form-select" id="idforma_pago" name="idforma_pago" required>
                                @foreach ($formaspago as $forma)
                                    <option value="{{ $forma->idforma_pago }}" {{ $transaccion->idforma_pago == $forma->idforma_pago ? 'selected' : '' }}>
                                        {{ $forma->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="campos-transferencia" style="display: none;">
                            <div class="mb-3">
                                <label for="alias_destinatario" class="form-label">Alias del Destinatario</label>
                                <input type="text" class="form-control" id="alias_destinatario" name="alias_destinatario" value="{{ $transaccion->alias_destinatario }}">
                            </div>
                            <div class="mb-3">
                                <label for="nombre_destinatario" class="form-label">Nombre del Destinatario</label>
                                <input type="text" class="form-control" id="nombre_destinatario" name="nombre_destinatario" value="{{ $transaccion->nombre_destinatario }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ $transaccion->descripcion }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <a href="{{ route('transaccions.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formaPagoSelect = document.getElementById('idforma_pago');
        const camposTransferencia = document.getElementById('campos-transferencia');

        function toggleCamposTransferencia() {
            const opcionSeleccionada = formaPagoSelect.options[formaPagoSelect.selectedIndex].text;
            if (opcionSeleccionada.toLowerCase().trim() === 'transferencia') {
                camposTransferencia.style.display = 'block';
            } else {
                camposTransferencia.style.display = 'none';
            }
        }

        toggleCamposTransferencia();

        formaPagoSelect.addEventListener('change', toggleCamposTransferencia);
    });
</script>
@endpush

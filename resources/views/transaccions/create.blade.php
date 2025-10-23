@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Registrar Nueva Transacción') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transaccions.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->idcategoria }}" data-tipo="{{ $categoria->tipo_categoria }}">
                                        {{ $categoria->nombre }} ({{ $categoria->tipo_categoria }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="forma-pago-group" class="mb-3">
                            <label for="idforma_pago" class="form-label">Forma de Pago</label>
                            <select class="form-select" id="idforma_pago" name="idforma_pago">
                                <option value="">Seleccione una forma de pago</option>
                                @foreach ($formaspago as $forma)
                                    <option value="{{ $forma->idforma_pago }}">{{ $forma->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="campos-transferencia" style="display: none;">
                            <div class="mb-3">
                                <label for="alias_destinatario" class="form-label">Alias del Destinatario</label>
                                <input type="text" class="form-control" id="alias_destinatario" name="alias_destinatario">
                            </div>
                            <div class="mb-3">
                                <label for="nombre_destinatario" class="form-label">Nombre del Destinatario</label>
                                <input type="text" class="form-control" id="nombre_destinatario" name="nombre_destinatario">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Transacción</button>
                        <a href="{{ url('/home') }}" class="btn btn-secondary">Cancelar</a>
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
        const categoriaSelect = document.getElementById('categoria_id');
        const formaPagoGroup = document.getElementById('forma-pago-group');
        const formaPagoInput = document.getElementById('idforma_pago');

        function toggleCamposTransferencia() {
            const opcionSeleccionada = formaPagoSelect.options[formaPagoSelect.selectedIndex].text;
            camposTransferencia.style.display = (opcionSeleccionada.toLowerCase().trim() === 'transferencia') ? 'block' : 'none';
        }

        function toggleFormaPago() {
            const opcionSeleccionada = categoriaSelect.options[categoriaSelect.selectedIndex];
            const tipoCategoria = opcionSeleccionada.dataset.tipo;

            if (tipoCategoria === 'Ingreso') {
                formaPagoGroup.style.display = 'none';
                formaPagoInput.required = false;
            } else {
                formaPagoGroup.style.display = 'block';
                formaPagoInput.required = true;
            }
        }

        formaPagoSelect.addEventListener('change', toggleCamposTransferencia);
        categoriaSelect.addEventListener('change', toggleFormaPago);

        toggleCamposTransferencia();
        toggleFormaPago();
    });
</script>
@endpush

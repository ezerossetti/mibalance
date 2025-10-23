<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Transacciones</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; } /* Tamaño de letra más chico para PDF */
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; padding: 6px; text-align: left; } /* Menos padding */
        th { background-color: #f2f2f2; }
        h1 { text-align: center; margin-bottom: 15px; font-size: 18px;} /* Título más chico */
        .text-success { color: green; }
        .text-danger { color: red; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Reporte de Transacciones</h1>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Categoría</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaccions as $transaccion)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $transaccion->categoria->nombre ?? 'N/A' }}</td>
                    <td>{{ $transaccion->categoria->tipo_categoria ?? 'N/A' }}</td>
                    <td>{{ $transaccion->descripcion }}</td>
                    <td class="text-right @if($transaccion->categoria && $transaccion->categoria->tipo_categoria == 'Ingreso') text-success @else text-danger @endif">
                        ${{ number_format($transaccion->monto, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No hay transacciones para mostrar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

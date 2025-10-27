<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Transaccion;
use App\Models\FormaPago;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class TransaccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->transaccions()->with('categoria');

        if ($request->filled('tipo')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('tipo_categoria', $request->tipo);
            });
        }
        if ($request->filled('categoria_id')) {
            $query->where('idcategoria', $request->categoria_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $transaccions = $query->orderBy('fecha', 'desc')->get();
        $categorias_filtro = Auth::user()->categorias;

        return view('transaccions.index', compact('transaccions', 'categorias_filtro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Auth::user()->categorias;
        $formaspago = FormaPago::all();
        return view('transaccions.create', compact('categorias', 'formaspago'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'monto' => 'required|numeric|min:0.01',
        'fecha' => 'required|date',
        'categoria_id' => 'required|exists:categoria,idcategoria',
        'idforma_pago' => 'nullable|exists:forma_pago,idforma_pago', // <-- CAMBIADO a nullable
        'descripcion' => 'nullable|string|max:255',
        'alias_destinatario' => 'nullable|string|max:45',
        'nombre_destinatario' => 'nullable|string|max:45'
        ]);

        $transaccion = new Transaccion();
        $transaccion->monto = $request->monto;
        $transaccion->fecha = $request->fecha;
        $transaccion->descripcion = $request->descripcion;
        $transaccion->idcategoria = $request->categoria_id;
        $transaccion->idUsuario = Auth::id();
        $transaccion->idforma_pago = $request->idforma_pago;
        $transaccion->alias_destinatario = $request->alias_destinatario;
        $transaccion->nombre_destinatario = $request->nombre_destinatario;
        $transaccion->save();

        // Verificamos solo si la transacción guardada es un Gasto
        $categoriaGuardada = Categoria::find($transaccion->idcategoria); // Necesitamos la categoría
        if ($categoriaGuardada && $categoriaGuardada->tipo_categoria == 'Gasto') {
        // Pasamos el ID de la categoría y la fecha (convertida a Carbon)
        $this->verificarAlertas($transaccion->idcategoria, Carbon::parse($transaccion->fecha));
}

        return redirect()->route('transaccions.index')->with('success', '¡Transacción(es) registrada(s) exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaccion $transaccion)
    {
        if ($transaccion->idUsuario !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        $categorias = Auth::user()->categorias;
        $formaspago = FormaPago::all();

        return view('transaccions.edit', compact('transaccion', 'categorias', 'formaspago'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaccion $transaccion)
    {
        if ($transaccion->idUsuario !== Auth::id()) {
        abort(403, 'Acción no autorizada.');
    }

    $request->validate([
        'monto' => 'required|numeric|min:0.01',
        'fecha' => 'required|date',
        'idcategoria' => 'required|exists:categoria,idcategoria',
        'idforma_pago' => 'required|exists:forma_pago,idforma_pago',
        'descripcion' => 'nullable|string|max:255',
        'alias_destinatario' => 'nullable|string|max:45',
        'nombre_destinatario' => 'nullable|string|max:45',
    ]);

    $transaccion->monto = $request->monto;
    $transaccion->fecha = $request->fecha;
    $transaccion->descripcion = $request->descripcion;
    $transaccion->idcategoria = $request->idcategoria;
    $transaccion->idforma_pago = $request->idforma_pago;
    $transaccion->alias_destinatario = $request->alias_destinatario;
    $transaccion->nombre_destinatario = $request->nombre_destinatario;
    $transaccion->save();

    return redirect()->route('transaccions.index')
                     ->with('success', '¡Transacción actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaccion $transaccion)
    {
        if ($transaccion->idUsuario !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }
        $transaccion->delete();

        // Obtenemos la categoría ANTES de borrar (si es borrado grupal, tomamos la de la $transaccion original)
        $categoriaBorrada = $transaccion->categoria;
        if ($categoriaBorrada && $categoriaBorrada->tipo_categoria == 'Gasto') {
        // Verificamos usando el ID de la categoría y la fecha de la transacción borrada
        $this->verificarAlertas($categoriaBorrada->idcategoria, Carbon::parse($transaccion->fecha));
}

        return redirect()->route('transaccions.index')
                         ->with('success', '¡Transacción eliminada exitosamente!');
    }

    public function exportPDF(Request $request)
{
    $query = Auth::user()->transaccions()->with('categoria');
    if ($request->filled('tipo')) {
        $query->whereHas('categoria', function ($q) use ($request) {
            $q->where('tipo_categoria', $request->tipo);
        });
    }
    if ($request->filled('categoria_id')) {
        $query->where('idcategoria', $request->categoria_id);
    }
    if ($request->filled('fecha_desde')) {
        $query->where('fecha', '>=', $request->fecha_desde);
    }
    if ($request->filled('fecha_hasta')) {
        $query->where('fecha', '<=', $request->fecha_hasta);
    }
    $transaccions = $query->orderBy('fecha', 'desc')->get();

    $pdf = Pdf::loadView('transaccions.pdf', compact('transaccions'));

    $fileName = 'reporte-transacciones-' . date('Ymd_His') . '.pdf';

    return $pdf->download($fileName);
}

public function exportCSV(Request $request)
    {
        $query = Auth::user()->transaccions()->with(['categoria', 'formaPago']);
        if ($request->filled('tipo')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('tipo_categoria', $request->tipo);
            });
        }
        if ($request->filled('categoria_id')) {
            $query->where('idcategoria', $request->categoria_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }
        $transaccions = $query->orderBy('fecha', 'desc')->get();

        $fileName = 'reporte-transacciones-' . date('Ymd_His') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['Fecha', 'Categoria', 'Tipo', 'Descripcion', 'Forma Pago', 'Monto'];

        $callback = function() use($transaccions, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($transaccions as $transaccion) {
                $row['Fecha']       = \Illuminate\Support\Carbon::parse($transaccion->fecha)->format('d/m/Y');
                $row['Categoria']   = $transaccion->categoria->nombre ?? 'N/A';
                $row['Tipo']        = $transaccion->categoria->tipo_categoria ?? 'N/A';
                $row['Descripcion'] = $transaccion->descripcion;
                $row['Forma Pago']  = $transaccion->formaPago->nombre ?? 'N/A';
                $row['Monto']       = number_format($transaccion->monto, 2, ',', '.');

                fputcsv($file, array($row['Fecha'], $row['Categoria'], $row['Tipo'], $row['Descripcion'], $row['Forma Pago'], $row['Monto']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    /**
 * Verifica si se disparó alguna alerta para una categoría y mes específicos.
 * Guarda un mensaje en la sesión si se supera el límite.
 *
 * @param int $idCategoria El ID de la categoría a verificar.
 * @param Carbon $fecha La fecha de la transacción (para saber el mes y año).
 * @return void
 */
private function verificarAlertas(int $idCategoria, Carbon $fecha)
{
    // Busca si hay una alerta activa para esta categoría y usuario
    $alerta = Alerta::where('idUsuario', Auth::id())
                    ->where('idcategoria', $idCategoria)
                    ->where('activa', true)
                    ->first(); // Obtenemos la primera alerta activa (debería ser única por categoría)

    // Si no hay alerta definida para esta categoría, no hacemos nada
    if (!$alerta) {
        return;
    }

    // Calculamos el total gastado en esta categoría durante el mes de la transacción
    $totalGastadoMes = Transaccion::where('idUsuario', Auth::id())
                            ->where('idcategoria', $idCategoria)
                            ->whereYear('fecha', $fecha->year)
                            ->whereMonth('fecha', $fecha->month)
                            ->whereHas('categoria', function ($q) { // Nos aseguramos de sumar solo gastos
                                $q->where('tipo_categoria', 'Gasto');
                            })
                            ->sum('monto');

    // Comparamos el total gastado con el límite de la alerta
    if ($totalGastadoMes > $alerta->limite) {
        // Si se superó el límite, guardamos un mensaje de advertencia en la sesión
        // Usamos 'warning' como clave para distinguirlo de los mensajes 'success'
        session()->flash('warning',
            "¡Alerta! Superaste el límite de $" . number_format($alerta->limite, 2, ',', '.') .
            " para la categoría '" . ($alerta->categoria->nombre ?? 'N/A') .
            "' este mes (Llevas gastado $" . number_format($totalGastadoMes, 2, ',', '.') . ")."
        );
    }
}
}

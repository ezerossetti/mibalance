<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Alerta;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() // Quitamos Request $request
    {
        $user = Auth::user();
        $now = Carbon::now();
        Carbon::setLocale('es'); // Forzamos español para los meses

        // --- Cálculos de las tarjetas (Mes Actual) ---
        $ingresosMes = $user->transaccions()->whereYear('fecha', $now->year)->whereMonth('fecha', $now->month)->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Ingreso'); })->sum('monto');
        $gastosMes = $user->transaccions()->whereYear('fecha', $now->year)->whereMonth('fecha', $now->month)->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Gasto'); })->sum('monto');

        // --- Saldo Total (Histórico) ---
        $totalIngresos = $user->transaccions()->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Ingreso'); })->sum('monto');
        $totalGastos = $user->transaccions()->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Gasto'); })->sum('monto');
        $saldoTotal = $totalIngresos - $totalGastos;

        // --- Transacciones Recientes (Últimas 5 del mes actual) ---
        $transaccionesRecientes = $user->transaccions()->with('categoria')
                                        ->whereYear('fecha', $now->year)->whereMonth('fecha', $now->month)
                                        ->latest('fecha')->take(5)->get();

        // --- Datos para el Gráfico de Torta (Gastos del Mes Actual por Categoría) ---
        $gastosPorCategoria = DB::table('transaccion')
            ->join('categoria', 'transaccion.idcategoria', '=', 'categoria.idcategoria')
            ->where('transaccion.idUsuario', $user->idUsuario)
            ->where(DB::raw('TRIM(categoria.tipo_categoria)'), '=', 'Gasto')
            ->whereYear('transaccion.fecha', $now->year)
            ->whereMonth('transaccion.fecha', $now->month)
            ->groupBy('categoria.nombre')
            ->select('categoria.nombre', DB::raw('SUM(transaccion.monto) as total'))
            ->orderBy('total', 'desc')
            ->get();
        $labelsGraficoTorta = $gastosPorCategoria->pluck('nombre');
        $dataGraficoTorta = $gastosPorCategoria->pluck('total');
        $topCategoriasGasto = $gastosPorCategoria->take(5);

        // --- Datos para el Gráfico de Tendencia (Últimos 6 Meses) ---
        $mesesTendencia = [];
        $ingresosTendencia = [];
        $gastosTendencia = [];
        for ($i = 5; $i >= 0; $i--) {
            $fechaMes = Carbon::now()->subMonths($i);
            $mesesTendencia[] = ucfirst($fechaMes->translatedFormat('M'));
            $ingresosTendencia[] = $user->transaccions()->whereYear('fecha', $fechaMes->year)->whereMonth('fecha', $fechaMes->month)->whereHas('categoria', function($q){ $q->where('tipo_categoria', 'Ingreso'); })->sum('monto');
            $gastosTendencia[] = $user->transaccions()->whereYear('fecha', $fechaMes->year)->whereMonth('fecha', $fechaMes->month)->whereHas('categoria', function($q){ $q->where('tipo_categoria', 'Gasto'); })->sum('monto');
        }

        // --- ¡NUEVA LÓGICA DE VERIFICACIÓN DE ALERTAS! ---

        // <-- CAMBIO 1: Renombramos esta variable para más claridad
        $alertasActivas = Alerta::where('idUsuario', $user->idUsuario)
                                     ->where('activa', 1)
                                     ->get();

        // <-- CAMBIO 2: Creamos un NUEVO array para los mensajes
        $alertasDisparadas = [];

        // <-- CAMBIO 3: Hacemos el loop sobre la lista de alertas ACTIVAS
        foreach ($alertasActivas as $alerta) {
            // Calculamos el total gastado ESTE MES para la categoría de la alerta
            $totalGastadoCategoria = $user->transaccions()
                                        ->where('idcategoria', $alerta->idcategoria)
                                        ->whereYear('fecha', $now->year)
                                        ->whereMonth('fecha', $now->month)
                                        ->whereHas('categoria', function ($q) {
                                            $q->where('tipo_categoria', 'Gasto');
                                        })
                                        ->sum('monto');

            // Si el gasto supera el límite, agregamos un mensaje al array
            if ($totalGastadoCategoria > $alerta->limite) {
                // <-- CAMBIO 4: Agregamos el mensaje al NUEVO array
                $alertasDisparadas[] = "Superaste el límite de $" . number_format($alerta->limite, 2, ',', '.') .
                                     " en la categoría '" . $alerta->categoria->nombre .
                                     "' (llevas $" . number_format($totalGastadoCategoria, 2, ',', '.') . ").";
            }
        }
        // --- FIN DE LA LÓGICA DE ALERTAS ---

        // --- Pasamos todo a la vista ---
        return view('home', compact(
            'ingresosMes', 'gastosMes', 'saldoTotal', 'transaccionesRecientes',
            'labelsGraficoTorta', 'dataGraficoTorta',
            'topCategoriasGasto',
            'mesesTendencia', 'ingresosTendencia', 'gastosTendencia',
            'alertasDisparadas' // <-- CAMBIO 5: Pasamos el array de MENSAJES a la vista
        ));
    }
}

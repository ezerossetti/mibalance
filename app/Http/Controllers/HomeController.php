<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
   public function index()
{
    $user = Auth::user();
    $now = Carbon::now();

    $ingresosMes = $user->transaccions()->whereYear('fecha', $now->year)->whereMonth('fecha', $now->month)->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Ingreso'); })->sum('monto');
    $gastosMes = $user->transaccions()->whereYear('fecha', $now->year)->whereMonth('fecha', $now->month)->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Gasto'); })->sum('monto');

    $totalIngresos = $user->transaccions()->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Ingreso'); })->sum('monto');
    $totalGastos = $user->transaccions()->whereHas('categoria', function ($q) { $q->where('tipo_categoria', 'Gasto'); })->sum('monto');
    $saldoTotal = $totalIngresos - $totalGastos;

    $transaccionesRecientes = $user->transaccions()->with('categoria')
                                ->whereYear('fecha', $now->year)->whereMonth('fecha', $now->month)
                                ->latest('fecha')->take(5)->get();

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

    $mesesTendencia = [];
    $ingresosTendencia = [];
    $gastosTendencia = [];
    for ($i = 5; $i >= 0; $i--) {
        $fechaMes = Carbon::now()->subMonths($i);
        $mesesTendencia[] = $fechaMes->translatedFormat('M');
        $ingresosTendencia[] = $user->transaccions()->whereYear('fecha', $fechaMes->year)->whereMonth('fecha', $fechaMes->month)->whereHas('categoria', function($q){ $q->where('tipo_categoria', 'Ingreso'); })->sum('monto');
        $gastosTendencia[] = $user->transaccions()->whereYear('fecha', $fechaMes->year)->whereMonth('fecha', $fechaMes->month)->whereHas('categoria', function($q){ $q->where('tipo_categoria', 'Gasto'); })->sum('monto');
    }

    return view('home', compact(
        'ingresosMes', 'gastosMes', 'saldoTotal', 'transaccionesRecientes',
        'labelsGraficoTorta', 'dataGraficoTorta',
        'topCategoriasGasto',
        'mesesTendencia', 'ingresosTendencia', 'gastosTendencia'
    ));
}
}




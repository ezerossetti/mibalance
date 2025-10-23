<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{

 public function index(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);
    $user = Auth::user();

    $datos = DB::table('transaccion')
        ->join('categoria', 'transaccion.idcategoria', '=', 'categoria.idcategoria')
        ->where('transaccion.idUsuario', $user->idUsuario)
        ->whereYear('transaccion.fecha', $year)
        ->select(
            DB::raw('MONTH(transaccion.fecha) as mes'),
            DB::raw("SUM(IF(categoria.tipo_categoria = 'Ingreso', transaccion.monto, 0)) as total_ingresos"),
            DB::raw("SUM(IF(categoria.tipo_categoria = 'Gasto', transaccion.monto, 0)) as total_gastos")
        )
        ->groupBy('mes')
        ->orderBy('mes', 'asc')
        ->get()
        ->keyBy('mes');

    $reporte = [];
    $mesesGrafico = [];
    $ingresosGrafico = [];
    $gastosGrafico = [];

    for ($i = 1; $i <= 12; $i++) {
        $nombreMes = Carbon::create()->month($i)->translatedFormat('F');
        $mesesGrafico[] = Carbon::create()->month($i)->translatedFormat('M');

        if (isset($datos[$i])) {
            $ingresos = $datos[$i]->total_ingresos;
            $gastos = $datos[$i]->total_gastos;
            $balance = $ingresos - $gastos;

            $reporte[$i] = compact('nombreMes', 'ingresos', 'gastos', 'balance');
            $ingresosGrafico[] = $ingresos;
            $gastosGrafico[] = $gastos;

        } else {
            $reporte[$i] = ['nombreMes' => $nombreMes, 'ingresos' => 0, 'gastos' => 0, 'balance' => 0];
            $ingresosGrafico[] = 0;
            $gastosGrafico[] = 0;
        }
    }

    $yearsWithTransactions = $user->transaccions()
                                  ->select(DB::raw('YEAR(fecha) as year'))
                                  ->distinct()
                                  ->orderBy('year', 'desc')
                                  ->pluck('year');

    return view('reportes.index', compact(
        'reporte',
        'yearsWithTransactions',
        'year',
        'mesesGrafico',
        'ingresosGrafico',
        'gastosGrafico'
    ));
}

}

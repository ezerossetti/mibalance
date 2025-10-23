<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\FormaPagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\PerfilController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('categorias', CategoriaController::class);
    Route::resource('transaccions', TransaccionController::class);
    Route::resource('formaspago', FormaPagoController::class);
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::get('/transaccions/export/pdf', [TransaccionController::class, 'exportPDF'])->name('transaccions.export.pdf');
    Route::get('/transaccions/export/csv', [TransaccionController::class, 'exportCSV'])->name('transaccions.export.csv');
});

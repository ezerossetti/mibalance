<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormaPago;

class FormaPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Buscamos todas las formas de pago que existen
    $formaspago = FormaPago::all();

    // Las enviamos a la vista
    return view('formaspago.index', compact('formaspago'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('formaspago.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|unique:forma_pago,nombre|max:50',
    ]);

    $formaPago = new FormaPago();
    $formaPago->nombre = $request->nombre;
    $formaPago->save();

    return redirect()->route('formaspago.index')
                     ->with('success', '¡Forma de pago creada exitosamente!');
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
    public function edit(FormaPago $formaspago)
{
    return view('formaspago.edit', compact('formaspago'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FormaPago $formaspago)
{
    $request->validate([
        'nombre' => 'required|string|max:50|unique:forma_pago,nombre,' . $formaspago->idforma_pago . ',idforma_pago',
    ]);

    $formaspago->nombre = $request->nombre;
    $formaspago->save();

    return redirect()->route('formaspago.index')
                     ->with('success', '¡Forma de pago actualizada exitosamente!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormaPago $formaspago)
{

    $formaspago->delete();

    return redirect()->route('formaspago.index')
                     ->with('success', '¡Forma de pago eliminada exitosamente!');
}
}

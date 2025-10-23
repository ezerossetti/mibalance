<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Auth::user()->categorias;
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:255',
        'tipo_categoria' => 'required|in:Gasto,Ingreso,Ambos',
    ]);

    $categoria = new Categoria();
    $categoria->nombre = $request->nombre;
    $categoria->descripcion = $request->descripcion;
    $categoria->tipo_categoria = $request->tipo_categoria;
    $categoria->idUsuario = Auth::id(); // Asigna el ID del usuario actual
    $categoria->save();

    return redirect()->route('categorias.index')
                     ->with('success', '¡Categoría creada exitosamente!');
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
    public function edit(Categoria $categoria)
{
    if ($categoria->idUsuario !== Auth::id()) {
        abort(403);
    }
    return view('categorias.edit', compact('categoria'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
{
    if ($categoria->idUsuario !== Auth::id()) {
        abort(403);
    }

    $request->validate([
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:255',
        'tipo_categoria' => 'required|in:Gasto,Ingreso,Ambos',
    ]);

    $categoria->nombre = $request->nombre;
    $categoria->descripcion = $request->descripcion;
    $categoria->tipo_categoria = $request->tipo_categoria;
    $categoria->save();

    return redirect()->route('categorias.index')
                     ->with('success', '¡Categoría actualizada exitosamente!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
{
    if ($categoria->idUsuario !== Auth::id()) {
        abort(403);
    }


    $categoria->delete();

    return redirect()->route('categorias.index')
                     ->with('success', '¡Categoría eliminada exitosamente!');
}
}

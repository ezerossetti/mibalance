<?php

namespace App\Http\Controllers;

use App\Models\Alerta; // Para usar el modelo Alerta
use App\Models\Categoria;
use Illuminate\Http\Request; // Para manejar requests (lo usaremos después)
use Illuminate\Support\Facades\Auth; // Para obtener el usuario logueado
use Illuminate\Validation\Rule; // Necesario para validación unique

class AlertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
   {
    // Buscamos las alertas que pertenecen al usuario logueado
    // Eager load ('with') la relación 'categoria' para mostrar su nombre eficientemente
    $alertas = Auth::user()->alertas()->with('categoria')->get();

    // Enviamos las alertas encontradas a la vista
    return view('alertas.index', compact('alertas'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtenemos las categorías de Gasto del usuario para el select
    // (Por ahora, las alertas son solo para gastos)
    $categoriasGasto = Auth::user()->categorias()->where('tipo_categoria', 'Gasto')->get();

    return view('alertas.create', compact('categoriasGasto'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validamos los datos del formulario
    $request->validate([
        'idcategoria' => 'required|exists:categoria,idcategoria',
        'limite' => 'required|numeric|min:0.01',
    ]);

    // Verificamos que el usuario no cree una alerta duplicada para la misma categoría
    $existe = Alerta::where('idUsuario', Auth::id())
                    ->where('idcategoria', $request->idcategoria)
                    ->exists();

    if ($existe) {
        return back()->withErrors(['idcategoria' => 'Ya existe una alerta definida para esta categoría.'])->withInput();
    }

    // Creamos la nueva alerta
    Alerta::create([
        'idUsuario' => Auth::id(),
        'idcategoria' => $request->idcategoria,
        'limite' => $request->limite,
        'tipo' => 'gasto_mayor_a', // Por ahora solo este tipo
        'activa' => true,
    ]);

    return redirect()->route('alertas.index')
                     ->with('success', '¡Alerta creada exitosamente!');
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
    public function edit(Alerta $alerta)
{
    // Verifica que la alerta pertenezca al usuario
    if ($alerta->idUsuario !== Auth::id()) {
        abort(403);
    }

    // Obtenemos las categorías de Gasto del usuario
    $categoriasGasto = Auth::user()->categorias()->where('tipo_categoria', 'Gasto')->get();

    return view('alertas.edit', compact('alerta', 'categoriasGasto'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alerta $alerta)
{
    // Verifica autorización
    if ($alerta->idUsuario !== Auth::id()) {
        abort(403);
    }

    // Valida los datos
    $request->validate([
        'idcategoria' => [
            'required',
            'exists:categoria,idcategoria',
            // Asegura que no haya otra alerta para la misma categoría (ignorando la actual)
            Rule::unique('alertas', 'idcategoria')->where('idUsuario', Auth::id())->ignore($alerta->id),
        ],
        'limite' => 'required|numeric|min:0.01',
        'activa' => 'boolean', // Para poder activar/desactivar
    ]);

    // Actualiza la alerta
    $alerta->idcategoria = $request->idcategoria;
    $alerta->limite = $request->limite;
    $alerta->activa = $request->has('activa'); // Si el checkbox 'activa' está marcado, será true
    $alerta->save();

    return redirect()->route('alertas.index')
                     ->with('success', '¡Alerta actualizada exitosamente!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alerta $alerta)
{
    // Verifica autorización
    if ($alerta->idUsuario !== Auth::id()) {
        abort(403);
    }

    $alerta->delete();

    return redirect()->route('alertas.index')
                     ->with('success', '¡Alerta eliminada exitosamente!');
}
}

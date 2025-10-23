<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('perfil.edit', compact('user'));
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuario,email,' . $user->idUsuario . ',idUsuario',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->nombre = $request->nombre;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->contrasena = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('perfil.edit')->with('success', '¡Perfil actualizado exitosamente!');
    }
}

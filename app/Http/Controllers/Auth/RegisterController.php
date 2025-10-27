<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Necesario para la función registered
use Illuminate\Auth\Events\Registered; // Necesario para la función registered
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * @var string
     */
    protected $redirectTo = '/home'; // O '/dashboard' si lo cambiaste

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Valida los datos del formulario de registro
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            // Verifica que el email sea único en la tabla 'usuario'
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuario'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Crea y guarda el nuevo usuario
        return User::create([
            // Guarda el valor del campo 'name' del form en la columna 'nombre' de la BD
            'nombre' => $data['name'],
            'email' => $data['email'],
            // Guarda la contraseña encriptada en la columna 'password'
            'password' => Hash::make($data['password']), // Usa 'password', no 'contrasena'
        ]);
    }

    /**
     * The user has been registered.
     * Se ejecuta después de un registro exitoso para añadir el mensaje flash.
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Añade un mensaje temporal a la sesión
        $request->session()->flash('success', '¡Te has registrado exitosamente! Bienvenido.');

        // Continúa con la redirección normal
        return null;
    }

} // Fin de la clase

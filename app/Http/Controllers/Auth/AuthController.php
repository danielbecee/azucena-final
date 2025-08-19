<?php
// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Muestra el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesa el login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirige según rol
            $role = Auth::user()->role->name;
            return $role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('employee.dashboard');
        }

        return back()
            ->withErrors(['email' => 'Las credenciales no coinciden.'])
            ->onlyInput('email');
    }

    // Cierra sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('signin.auth');
    }

    // Muestra el formulario de registro
    public function showSignupForm()
    {
        return view('auth.register');
    }

    // Procesa el registro
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required','string','max:100'],
            'email'                 => ['required','email','unique:users,email'],
            'password'              => ['required','confirmed','min:8'],
        ]);

        // Creamos el usuario con rol customer
        $roleId = Role::where('name','customer')->value('id');
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $roleId,
        ]);

        // Creamos su ficha de cliente
        Customer::create([
            'user_id'    => $user->id,
            'first_name' => $data['name'],
        ]);

        // Logueamos automáticamente
        Auth::login($user);
        return redirect()->route('employee.dashboard');
    }
}

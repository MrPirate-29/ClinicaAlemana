<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLogin()
    {
        // Si ya está autenticado, redirigir a su módulo
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Procesa las credenciales del formulario.
     *
     * Flujo:
     *  1. Valida campos requeridos
     *  2. Busca usuario por coe_usu
     *  3. Verifica contraseña con Hash::check
     *  4. Verifica estado ACTIVO
     *  5. Verifica que el rol seleccionado coincide con el rol real del usuario
     *  6. Inicia sesión y redirige al módulo correspondiente
     *
     * Se usa Auth::login() en lugar de Auth::attempt() porque los campos
     * de la BD no siguen la convención email/password de Laravel.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'coe_usu' => ['required', 'email'],
            'con_usu' => ['required'],
        ], [
            'coe_usu.required' => 'El correo es obligatorio.',
            'coe_usu.email'    => 'Ingresa un correo válido.',
            'con_usu.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar usuario por correo
        $usuario = Usuario::where('coe_usu', $request->coe_usu)->first();

        // Validar existencia y contraseña
        if (! $usuario || ! Hash::check($request->con_usu, $usuario->con_usu)) {
            return back()
                ->withErrors(['coe_usu' => 'Correo o contraseña incorrectos.'])
                ->withInput($request->only('coe_usu'));
        }

        // Validar que la cuenta esté activa
        if ($usuario->est_usu !== 'ACTIVO') {
            return back()
                ->withErrors(['coe_usu' => 'Tu cuenta está ' . strtolower($usuario->est_usu) . '. Contacta al administrador.'])
                ->withInput($request->only('coe_usu'));
        }

        // Iniciar sesión — el rol se determina automáticamente desde la BD
        Auth::login($usuario, (bool) $request->remember);
        $request->session()->regenerate();

        return $this->redirectByRole($usuario);
    }

    /**
     * Cierra la sesión del usuario activo.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirige al módulo correspondiente según el rol del usuario.
     */
    private function redirectByRole(Usuario $usuario): RedirectResponse
    {
        $nomRol = DB::table('rol')
            ->where('id_rol', $usuario->id_rol)
            ->value('nom_rol');

        return match ($nomRol) {
            'Administrador' => redirect()->route('admin.dashboard')
                                         ->with('success_alert', '¡Bienvenido, ' . $usuario->nom_usu . '!'),
            'Medico'        => redirect()->route('medico.panel')
                                         ->with('success_alert', '¡Bienvenido, Dr. ' . $usuario->nom_usu . '!'),
            'Gestora'       => redirect()->route('gestora.panel')
                                         ->with('success_alert', '¡Bienvenida, ' . $usuario->nom_usu . '!'),
            default         => redirect()->route('home'),
        };
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $user_google = Socialite::driver('google')->user();

            $se_registro = User::where('email', $user_google->email)->where('activo', '!=', 0)->first();
            if (empty($se_registro)) {
                $isUDHEmail = str_ends_with($user_google->email, '@udh.edu.pe');
                if (!$isUDHEmail) {
                    return redirect()->route('login')->with('error', 'Ingrese un correo institucional de la UDH.');
                }
                $user = new User();
                $user->email = $user_google->email;
                $user->google_id = $user_google->id;
                $user->name = $user_google->user['given_name'] ?? $user_google->name;
                $user->apellidos = $user_google->user['family_name'] ?? '';
                $user->password = bcrypt(usuarioCorreo($user_google->email));
                $user->email_verified_at = now();
                $user->assignRole('Usuario');
                $user->save();
                Auth::login($user);
                return redirect()->route('user-dashboard')->with('success', 'Ha iniciado sesión correctamente ');
            } else {
                if ($se_registro->estado == 2) {
                    return redirect()->redirect('login')->with('error', 'Su cuenta se encuentra suspendido');
                }
                Auth::login($se_registro);
            }
            // Aca extraeremos el nombre del rol...
            $roleName = Auth::user()->rol->rol_nombre;

            // Para luego compararlo con una condicional y poder redirigir a su ruta correcta...
            if ($roleName === 'Admin') {
                return redirect()->intended(RouteServiceProvider::$ADMINHOME)
                    ->with('success', 'Ha iniciado sesión como administrador.');
            } elseif ($roleName === 'Soporte') {
                return redirect()->intended(RouteServiceProvider::$SUPPORTHOME)
                    ->with('success', 'Ha iniciado sesión como soporte técnico.');
            } else {
                return redirect()->intended(RouteServiceProvider::$USERHOME)
                    ->with('success', 'Ha iniciado sesión como usuario.');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Ocurrió un error al iniciar sesión.');
        }
    }
}

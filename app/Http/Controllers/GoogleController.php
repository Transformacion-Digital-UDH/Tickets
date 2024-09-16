<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

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
                    return redirect()->route('login')->with('status', 'Se requiere un correo institucional de la UDH.');
                }
                $user = new User();
                $user->email = $user_google->email;
                $user->google_id = $user_google->id;
                $user->name = $user_google->user['given_name'] ?? $user_google->name;
                $user->apellidos = $user_google->user['family_name'] ?? '';
                $user->password = bcrypt(usuarioCorreo($user_google->email));
                $user->email_verified_at = now();
                $user->save();
                Auth::login($user);
                return redirect()->route('dashboard');
            } else {
                if ($se_registro->estado == 2) {
                    return redirect()->redirect('login')->with('status', 'Su cuenta se encuentra suspendido');
                }
                Auth::login($se_registro);
                return redirect()->intended(RouteServiceProvider::HOME);
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('status', 'Ocurrió un error al iniciar sesión.');
        }
    }
}

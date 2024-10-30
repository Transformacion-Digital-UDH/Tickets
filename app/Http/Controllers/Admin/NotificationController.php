<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'notificaciones' => $request->user()->unreadNotifications,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function marcarNotificacionesComoLeidas()
    {
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Notificaciones marcadas como leídas.']);
    }
}

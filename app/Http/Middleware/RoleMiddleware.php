<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- TAMBAHIN INI
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $guard = match($role) {
            'admin' => 'admin',
            'staff' => 'staff',
            'student' => 'student',
            default => 'web',
        };

        // Cek guard yang diminta dulu
        if (Auth::guard($guard)->check()) {
            $user = Auth::guard($guard)->user();
            
            if ($user->user_role === $role) {
                auth()->setDefaultDriver($guard);
                return $next($request);
            }
        }

        // Kalau guard ini gak aktif, cek apakah ada guard lain yang aktif
        $otherGuards = ['admin', 'staff', 'student', 'web'];
        foreach ($otherGuards as $g) {
            if ($g !== $guard && Auth::guard($g)->check()) {
                // JANGAN redirect, kasih 403 aja biar jelas
                return response()->view('errors.403', [], 403);
            }
        }

        // Belum login sama sekali
        return redirect('/')->with('showLogin', true);
    }
}
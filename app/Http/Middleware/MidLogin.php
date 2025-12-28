<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MidLogin
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda Belum Login');
        }

        // Jika tidak ada role yang ditentukan, lanjutkan
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah role user sesuai dengan yang diizinkan
        $userRole = Auth::user()->role;
        
        // 🔍 DEBUG LOG - PENTING!
        Log::info('=== MIDDLEWARE CHECK ===');
        Log::info('URL: ' . $request->url());
        Log::info('User ID: ' . Auth::id());
        Log::info('User Role: ' . $userRole);
        Log::info('User Role Type: ' . gettype($userRole));
        Log::info('User Role Length: ' . strlen($userRole));
        Log::info('Required Roles: ' . json_encode($roles));
        Log::info('Is Allowed: ' . (in_array($userRole, $roles) ? 'YES' : 'NO'));
        
        // Debug setiap role
        foreach ($roles as $index => $role) {
            Log::info("Role[$index]: '$role' (type: " . gettype($role) . ", length: " . strlen($role) . ")");
            Log::info("Match with userRole: " . ($userRole === $role ? 'YES' : 'NO'));
        }
        
        if (!in_array($userRole, $roles)) {
            Log::error('ACCESS DENIED!');
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        Log::info('ACCESS GRANTED!');
        return $next($request);
    }
}
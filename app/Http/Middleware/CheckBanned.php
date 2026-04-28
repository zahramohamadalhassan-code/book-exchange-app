<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware لفحص حالة الحظر للمستخدم
 * في حال كان المستخدم محظوراً، يتم تسجيل خروجه وإعادة توجيهه
 */
class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'تم حظر حسابك. يرجى التواصل مع إدارة المنصة.');
        }

        return $next($request);
    }
}

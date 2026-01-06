<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserApproved
{
   public function handle(Request $request, Closure $next)
{
    $user = $request->user();

    // If somehow no user, let other middleware handle it
    if (! $user) {
        return $next($request);
    }

    // ✅ APPROVED RULE (match your DB)
    $isApproved = ($user->approval_status === 'approved') || !is_null($user->approved_at);

    if ($isApproved) {
        return $next($request);
    }

    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('status', 'Your account is pending admin approval.');
}

}

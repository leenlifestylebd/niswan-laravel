<?php

namespace App\Http\Middleware;

use App\Services\AdminAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// /admin/* সুরক্ষা — সেশনে লগইন ফ্ল্যাগ না থাকলে login পেজে পাঠায়
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get(AdminAuthService::SESSION_KEY)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;

// ভিজিট beacon গ্রহণ করে DB-তে log করে। public, দ্রুত, ব্যর্থ হলেও চুপচাপ।
class TrackController extends Controller
{
    public function store(Request $request, AnalyticsService $analytics)
    {
        try {
            $payload = $request->json()->all() ?: $request->all();
            $path    = $payload['path'] ?? '/';

            // admin পেজ ট্র্যাক করি না
            if (is_string($path) && str_starts_with($path, '/admin')) {
                return response()->json(['ok' => true]);
            }

            $analytics->logVisit(
                $path,
                $payload['ref'] ?? $request->header('referer'),
                $request->ip(),
                $request->userAgent(),
            );
        } catch (\Throwable $e) {
            // analytics ব্যর্থ হলেও সাইট চলবে
        }

        return response()->json(['ok' => true]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    /** প্রিসেট রেঞ্জ → দিনের সংখ্যা */
    private const RANGES = [
        'today' => 0,
        '7'     => 7,
        '30'    => 30,
        '90'    => 90,
        '365'   => 365,
    ];

    public function index(Request $request, AnalyticsService $analytics)
    {
        $range = (string) $request->query('range', '30');
        $from  = $request->query('from');
        $to    = $request->query('to');

        if ($from || $to) {
            // custom from–to (তারিখ ইনপুট থেকে)
            $range = 'custom';
            $start = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
            $end   = $to ? Carbon::parse($to)->endOfDay() : Carbon::now();
        } elseif ($range === 'today') {
            $start = Carbon::now()->startOfDay();
            $end   = Carbon::now();
        } else {
            $days  = self::RANGES[$range] ?? 30;
            $range = (string) ($days ?: 30);
            $start = Carbon::now()->subDays($days)->startOfDay();
            $end   = Carbon::now();
        }

        return view('admin.analytics', [
            'range'    => $range,
            'from'     => $start,
            'to'       => $end,
            'visitors' => $analytics->visitorStats($start, $end),
            'orders'   => $analytics->orderStats($start, $end),
        ]);
    }
}

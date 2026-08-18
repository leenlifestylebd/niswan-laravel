<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// হালকা, প্রাইভেসি-বান্ধব ভিজিট ট্র্যাকিং — কোনো PII সেভ হয় না।
// visitor = hash(ip + ua + APP_KEY) → irreversible, শুধু unique গণনার জন্য।
class AnalyticsService
{
    public function visitorHash(?string $ip, ?string $ua): string
    {
        $salt = config('app.key') ?: 'store-analytics';

        return substr(hash('sha256', ($ip ?? '').'|'.($ua ?? '').'|'.$salt), 0, 24);
    }

    /** referrer URL → শুধু host (source): facebook / google / direct */
    private function refHost(?string $ref): string
    {
        if (! $ref) {
            return 'direct';
        }

        $host = parse_url($ref, PHP_URL_HOST);

        if (! $host) {
            return 'direct';
        }

        return preg_replace('/^www\./', '', $host) ?: 'direct';
    }

    private function deviceOf(?string $ua): string
    {
        return preg_match('/Mobile|Android|iPhone|iPad/i', (string) $ua) ? 'mobile' : 'desktop';
    }

    public function logVisit(?string $path, ?string $ref, ?string $ip, ?string $ua): void
    {
        Visit::create([
            'path'    => $path ?: '/',
            'ref'     => $this->refHost($ref),
            'visitor' => $this->visitorHash($ip, $ua),
            'device'  => $this->deviceOf($ua),
        ]);
    }

    /** রেঞ্জের দৈর্ঘ্য অনুযায়ী চার্টের bucket (day/week/month) */
    public function pickBucket(Carbon $from, Carbon $to): array
    {
        $days = max(1, (int) round($from->diffInSeconds($to, absolute: true) / 86400));

        if ($days <= 45) {
            return ['unit' => 'day', 'fmt' => 'MM-DD'];
        }
        if ($days <= 210) {
            return ['unit' => 'week', 'fmt' => 'MM-DD'];
        }

        return ['unit' => 'month', 'fmt' => 'YYYY-MM'];
    }

    /** অ্যাডমিন ড্যাশবোর্ডের জন্য সব ভিজিটর মেট্রিক */
    public function visitorStats(Carbon $from, Carbon $to): array
    {
        ['unit' => $unit, 'fmt' => $fmt] = $this->pickBucket($from, $to);

        $t = DB::selectOne('
            SELECT
              count(*) FILTER (WHERE created_at >= date_trunc(\'day\', now()))                   AS pv_today,
              count(DISTINCT visitor) FILTER (WHERE created_at >= date_trunc(\'day\', now()))    AS uv_today,
              count(DISTINCT visitor) FILTER (WHERE created_at >= now() - interval \'5 minutes\') AS live,
              count(*) FILTER (WHERE created_at >= ? AND created_at <= ?)                        AS pv_range,
              count(DISTINCT visitor) FILTER (WHERE created_at >= ? AND created_at <= ?)         AS uv_range
            FROM visits
        ', [$from, $to, $from, $to]);

        $daily = DB::select('
            SELECT to_char(date_trunc(?, created_at), ?) AS day,
                   count(*)::int AS pv,
                   count(DISTINCT visitor)::int AS uv
            FROM visits
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY 1 ORDER BY 1
        ', [$unit, $fmt, $from, $to]);

        $sources = DB::select('
            SELECT ref AS source, count(*)::int AS n
            FROM visits WHERE created_at >= ? AND created_at <= ?
            GROUP BY ref ORDER BY n DESC LIMIT 6
        ', [$from, $to]);

        $topPages = DB::select('
            SELECT path, count(*)::int AS n
            FROM visits WHERE created_at >= ? AND created_at <= ?
            GROUP BY path ORDER BY n DESC LIMIT 8
        ', [$from, $to]);

        $devices = DB::select('
            SELECT device, count(*)::int AS n
            FROM visits WHERE created_at >= ? AND created_at <= ?
            GROUP BY device
        ', [$from, $to]);

        return [
            'live'     => (int) ($t->live ?? 0),
            'pvToday'  => (int) ($t->pv_today ?? 0),
            'uvToday'  => (int) ($t->uv_today ?? 0),
            'pvRange'  => (int) ($t->pv_range ?? 0),
            'uvRange'  => (int) ($t->uv_range ?? 0),
            'daily'    => array_map(fn ($r) => ['day' => $r->day, 'pv' => $r->pv, 'uv' => $r->uv], $daily),
            'sources'  => array_map(fn ($r) => ['source' => $r->source, 'n' => $r->n], $sources),
            'topPages' => array_map(fn ($r) => ['path' => $r->path, 'n' => $r->n], $topPages),
            'devices'  => array_map(fn ($r) => ['device' => $r->device, 'n' => $r->n], $devices),
        ];
    }

    /** অর্ডার থেকে business metric */
    public function orderStats(Carbon $from, Carbon $to): array
    {
        ['unit' => $unit, 'fmt' => $fmt] = $this->pickBucket($from, $to);

        $t = DB::selectOne('
            SELECT
              count(*) FILTER (WHERE created_at >= date_trunc(\'day\', now()))                                AS orders_today,
              count(*) FILTER (WHERE created_at >= ? AND created_at <= ?)                                     AS orders_range,
              count(*)                                                                                        AS orders_total,
              count(*) FILTER (WHERE status = \'pending\'         AND created_at >= ? AND created_at <= ?)     AS pending,
              count(*) FILTER (WHERE status = \'confirmed\'       AND created_at >= ? AND created_at <= ?)     AS confirmed,
              count(*) FILTER (WHERE status = \'sent_to_courier\' AND created_at >= ? AND created_at <= ?)     AS sent,
              count(*) FILTER (WHERE status = \'delivered\'       AND created_at >= ? AND created_at <= ?)     AS delivered,
              count(*) FILTER (WHERE status = \'cancelled\'       AND created_at >= ? AND created_at <= ?)     AS cancelled,
              COALESCE(sum(total) FILTER (WHERE created_at >= date_trunc(\'day\', now())),0)                   AS rev_today,
              COALESCE(sum(total) FILTER (WHERE created_at >= ? AND created_at <= ?),0)                        AS rev_range,
              COALESCE(sum(total),0)                                                                           AS rev_total
            FROM orders
        ', [
            $from, $to, $from, $to, $from, $to, $from, $to, $from, $to, $from, $to, $from, $to,
        ]);

        $daily = DB::select('
            SELECT to_char(date_trunc(?, created_at), ?) AS day,
                   count(*)::int AS orders,
                   COALESCE(sum(total),0)::int AS rev
            FROM orders
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY 1 ORDER BY 1
        ', [$unit, $fmt, $from, $to]);

        $top = DB::select('
            SELECT product, count(*)::int AS n, COALESCE(sum(total),0)::int AS rev
            FROM orders
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY product ORDER BY n DESC LIMIT 6
        ', [$from, $to]);

        $ordersRange = (int) ($t->orders_range ?? 0);
        $revRange    = (int) ($t->rev_range ?? 0);

        return [
            'ordersToday' => (int) ($t->orders_today ?? 0),
            'ordersRange' => $ordersRange,
            'ordersTotal' => (int) ($t->orders_total ?? 0),
            'revToday'    => (int) ($t->rev_today ?? 0),
            'revRange'    => $revRange,
            'revTotal'    => (int) ($t->rev_total ?? 0),
            'aov'         => $ordersRange ? (int) round($revRange / $ordersRange) : 0,
            'status'      => [
                'pending'   => (int) ($t->pending ?? 0),
                'confirmed' => (int) ($t->confirmed ?? 0),
                'sent'      => (int) ($t->sent ?? 0),
                'delivered' => (int) ($t->delivered ?? 0),
                'cancelled' => (int) ($t->cancelled ?? 0),
            ],
            'daily' => array_map(fn ($r) => ['day' => $r->day, 'orders' => $r->orders, 'rev' => $r->rev], $daily),
            'top'   => array_map(fn ($r) => ['product' => $r->product, 'n' => $r->n, 'rev' => $r->rev], $top),
        ];
    }
}

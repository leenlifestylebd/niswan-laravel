<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// হালকা, প্রাইভেসি-বান্ধব ভিজিট ট্র্যাকিং — কোনো PII সেভ হয় না।
// visitor = hash(ip + ua + APP_KEY) → irreversible, শুধু unique গণনার জন্য।
//
// ⚠️ পরিসংখ্যানের কোয়েরিগুলো PostgreSQL ও MySQL/MariaDB দুই ইঞ্জিনেই চলে।
//    ইঞ্জিন-ভেদে যেসব অংশ আলাদা (FILTER, date_trunc, interval …) সেগুলো
//    নিচের ছোট হেল্পারগুলো তৈরি করে দেয়।
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
            'path'    => mb_substr($path ?: '/', 0, 191),
            'ref'     => mb_substr($this->refHost($ref), 0, 191),
            'visitor' => $this->visitorHash($ip, $ua),
            'device'  => $this->deviceOf($ua),
        ]);
    }

    // ── ইঞ্জিন-ভেদে SQL টুকরো ────────────────────────────────────────────

    private function isPgsql(): bool
    {
        return DB::connection()->getDriverName() === 'pgsql';
    }

    /** শর্তসাপেক্ষ গণনা — pg: FILTER, mysql: SUM(CASE WHEN …) */
    private function countIf(string $cond): string
    {
        return $this->isPgsql()
            ? "count(*) FILTER (WHERE {$cond})"
            : "SUM(CASE WHEN {$cond} THEN 1 ELSE 0 END)";
    }

    /** শর্তসাপেক্ষ unique গণনা */
    private function countDistinctIf(string $col, string $cond): string
    {
        return $this->isPgsql()
            ? "count(DISTINCT {$col}) FILTER (WHERE {$cond})"
            : "COUNT(DISTINCT CASE WHEN {$cond} THEN {$col} END)";
    }

    /** শর্তসাপেক্ষ যোগফল (খালি হলে 0) */
    private function sumIf(string $col, string $cond): string
    {
        return $this->isPgsql()
            ? "COALESCE(sum({$col}) FILTER (WHERE {$cond}),0)"
            : "COALESCE(SUM(CASE WHEN {$cond} THEN {$col} END),0)";
    }

    /** আজকের শুরু */
    private function startOfToday(): string
    {
        return $this->isPgsql() ? "date_trunc('day', now())" : 'CURDATE()';
    }

    /** এখন থেকে ৫ মিনিট আগে (লাইভ ভিজিটর) */
    private function fiveMinutesAgo(): string
    {
        return $this->isPgsql() ? "now() - interval '5 minutes'" : 'NOW() - INTERVAL 5 MINUTE';
    }

    /** চার্টের bucket লেবেল — day/week/month অনুযায়ী */
    private function bucketExpr(string $unit): string
    {
        if ($this->isPgsql()) {
            $fmt = $unit === 'month' ? 'YYYY-MM' : 'MM-DD';

            return "to_char(date_trunc('{$unit}', created_at), '{$fmt}')";
        }

        return match ($unit) {
            'month' => "DATE_FORMAT(created_at, '%Y-%m')",
            // সপ্তাহের শুরুর দিন (সোমবার) — Postgres এর date_trunc('week') এর সমতুল্য
            'week'  => "DATE_FORMAT(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY), '%m-%d')",
            default => "DATE_FORMAT(created_at, '%m-%d')",
        };
    }

    /** রেঞ্জের দৈর্ঘ্য অনুযায়ী চার্টের bucket (day/week/month) */
    public function pickBucket(Carbon $from, Carbon $to): array
    {
        $days = max(1, (int) round($from->diffInSeconds($to, absolute: true) / 86400));

        if ($days <= 45) {
            return ['unit' => 'day'];
        }
        if ($days <= 210) {
            return ['unit' => 'week'];
        }

        return ['unit' => 'month'];
    }

    // ── পরিসংখ্যান ───────────────────────────────────────────────────────

    /** অ্যাডমিন ড্যাশবোর্ডের জন্য সব ভিজিটর মেট্রিক */
    public function visitorStats(Carbon $from, Carbon $to): array
    {
        ['unit' => $unit] = $this->pickBucket($from, $to);

        $today  = $this->startOfToday();
        $live   = $this->fiveMinutesAgo();
        $inRange = 'created_at >= ? AND created_at <= ?';

        $t = DB::selectOne('
            SELECT
              '.$this->countIf("created_at >= {$today}").'                  AS pv_today,
              '.$this->countDistinctIf('visitor', "created_at >= {$today}").' AS uv_today,
              '.$this->countDistinctIf('visitor', "created_at >= {$live}").'  AS live,
              '.$this->countIf($inRange).'                                  AS pv_range,
              '.$this->countDistinctIf('visitor', $inRange).'                AS uv_range
            FROM visits
        ', [$from, $to, $from, $to]);

        $bucket = $this->bucketExpr($unit);

        $daily = DB::select("
            SELECT {$bucket} AS day, count(*) AS pv, count(DISTINCT visitor) AS uv
            FROM visits
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY 1 ORDER BY 1
        ", [$from, $to]);

        $sources = DB::select('
            SELECT ref AS source, count(*) AS n
            FROM visits WHERE created_at >= ? AND created_at <= ?
            GROUP BY ref ORDER BY n DESC LIMIT 6
        ', [$from, $to]);

        $topPages = DB::select('
            SELECT path, count(*) AS n
            FROM visits WHERE created_at >= ? AND created_at <= ?
            GROUP BY path ORDER BY n DESC LIMIT 8
        ', [$from, $to]);

        $devices = DB::select('
            SELECT device, count(*) AS n
            FROM visits WHERE created_at >= ? AND created_at <= ?
            GROUP BY device
        ', [$from, $to]);

        return [
            'live'     => (int) ($t->live ?? 0),
            'pvToday'  => (int) ($t->pv_today ?? 0),
            'uvToday'  => (int) ($t->uv_today ?? 0),
            'pvRange'  => (int) ($t->pv_range ?? 0),
            'uvRange'  => (int) ($t->uv_range ?? 0),
            'daily'    => array_map(fn ($r) => ['day' => $r->day, 'pv' => (int) $r->pv, 'uv' => (int) $r->uv], $daily),
            'sources'  => array_map(fn ($r) => ['source' => $r->source, 'n' => (int) $r->n], $sources),
            'topPages' => array_map(fn ($r) => ['path' => $r->path, 'n' => (int) $r->n], $topPages),
            'devices'  => array_map(fn ($r) => ['device' => $r->device, 'n' => (int) $r->n], $devices),
        ];
    }

    /** অর্ডার থেকে business metric */
    public function orderStats(Carbon $from, Carbon $to): array
    {
        ['unit' => $unit] = $this->pickBucket($from, $to);

        $today   = $this->startOfToday();
        $inRange = 'created_at >= ? AND created_at <= ?';
        $status  = fn (string $s) => "status = '{$s}' AND {$inRange}";

        // প্যারামিটারের ক্রম নিচের SELECT এর ক্রমের সাথে মিলিয়ে রাখতে হবে
        $params = [];
        for ($i = 0; $i < 7; $i++) {
            $params[] = $from;
            $params[] = $to;
        }

        $t = DB::selectOne('
            SELECT
              '.$this->countIf("created_at >= {$today}").'          AS orders_today,
              '.$this->countIf($inRange).'                          AS orders_range,
              count(*)                                              AS orders_total,
              '.$this->countIf($status('pending')).'                AS pending,
              '.$this->countIf($status('confirmed')).'              AS confirmed,
              '.$this->countIf($status('sent_to_courier')).'        AS sent,
              '.$this->countIf($status('delivered')).'              AS delivered,
              '.$this->countIf($status('cancelled')).'              AS cancelled,
              '.$this->sumIf('total', "created_at >= {$today}").'   AS rev_today,
              '.$this->sumIf('total', $inRange).'                   AS rev_range,
              COALESCE(sum(total),0)                                AS rev_total
            FROM orders
        ', $params);

        $bucket = $this->bucketExpr($unit);

        $daily = DB::select("
            SELECT {$bucket} AS day, count(*) AS orders, COALESCE(sum(total),0) AS rev
            FROM orders
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY 1 ORDER BY 1
        ", [$from, $to]);

        $top = DB::select('
            SELECT product, count(*) AS n, COALESCE(sum(total),0) AS rev
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
            'daily' => array_map(fn ($r) => ['day' => $r->day, 'orders' => (int) $r->orders, 'rev' => (int) $r->rev], $daily),
            'top'   => array_map(fn ($r) => ['product' => $r->product, 'n' => (int) $r->n, 'rev' => (int) $r->rev], $top),
        ];
    }
}

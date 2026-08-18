<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SettingsService;
use App\Services\SteadfastService;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'confirmed', 'sent_to_courier', 'delivered', 'cancelled'];

    /** ড্যাশবোর্ড — অর্ডার লিস্ট + স্ট্যাটাস স্ট্যাট */
    public function index(Request $request, SteadfastService $steadfast)
    {
        $orders = Order::orderByDesc('created_at')->limit(200)->get();

        $stats = [
            'total' => $orders->count(),
        ] + collect(self::STATUSES)
            ->mapWithKeys(fn ($s) => [$s => $orders->where('status', $s)->count()])
            ->all();

        return view('admin.orders', [
            'orders'          => $orders,
            'stats'           => $stats,
            'steadfastReady'  => $steadfast->ready(),
        ]);
    }

    /** অর্ডার এডিট — product/size/qty/area/charge/total/ঠিকানা */
    public function update(Request $request, int $id, SettingsService $settings)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'name'            => ['required', 'string'],
            'phone'           => ['required', 'string'],
            'address'         => ['required', 'string'],
            'product'         => ['nullable', 'string'],
            'size'            => ['nullable', 'string'],
            'qty'             => ['nullable', 'integer', 'min:1'],
            'area'            => ['nullable', 'string'],
            'delivery_charge' => ['nullable', 'integer', 'min:0'],
            'total'           => ['nullable', 'integer', 'min:0'],
        ]);

        if (! Phone::isValid($data['phone'])) {
            return back()->withErrors(['phone' => 'সঠিক ১১ ডিজিটের ফোন দিন'])->withInput();
        }

        $order->update([
            'name'            => $data['name'],
            'phone'           => $data['phone'],
            'address'         => $data['address'],
            'product'         => $data['product'] ?? null,
            'size'            => $data['size'] ?? null,
            'qty'             => (int) ($data['qty'] ?? 1),
            'area'            => $data['area'] ?? null,
            'delivery_charge' => (int) ($data['delivery_charge'] ?? 0),
            'total'           => (int) ($data['total'] ?? 0),
        ]);

        return back()->with('status', "অর্ডার #{$order->id} আপডেট হয়েছে");
    }

    /** স্ট্যাটাস বদল */
    public function status(Request $request)
    {
        $data = $request->validate([
            'id'     => ['required', 'integer'],
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        Order::whereKey($data['id'])->update(['status' => $data['status']]);

        return back()->with('status', 'স্ট্যাটাস বদলে গেছে');
    }

    /** এক ক্লিকে Steadfast এ পাঠানো */
    public function steadfast(Request $request, SteadfastService $steadfast)
    {
        if (! $steadfast->ready()) {
            return back()->withErrors(['steadfast' => 'Steadfast API key সেট করা নেই']);
        }

        $id    = (int) $request->input('id');
        $order = Order::find($id);

        if (! $order) {
            return back()->withErrors(['steadfast' => 'অর্ডার পাওয়া যায়নি']);
        }
        if ($order->consignment_id) {
            return back()->withErrors(['steadfast' => 'এই অর্ডার আগেই পাঠানো হয়েছে']);
        }

        try {
            $c = $steadfast->createConsignment($order);
            $order->update([
                'status'         => 'sent_to_courier',
                'consignment_id' => (string) ($c['consignment_id'] ?? ''),
                'tracking_code'  => $c['tracking_code'] ?? null,
            ]);

            return back()->with('status', "অর্ডার #{$order->id} কুরিয়ারে পাঠানো হয়েছে (".($c['tracking_code'] ?? '').')');
        } catch (\Throwable $e) {
            Log::error('Steadfast ব্যর্থ: '.$e->getMessage());

            return back()->withErrors(['steadfast' => $e->getMessage()]);
        }
    }

    /** একাধিক অর্ডার এক ক্লিকে Steadfast এ */
    public function steadfastBulk(Request $request, SteadfastService $steadfast)
    {
        if (! $steadfast->ready()) {
            return back()->withErrors(['steadfast' => 'Steadfast API key সেট করা নেই']);
        }

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (! $ids) {
            return back()->withErrors(['steadfast' => 'অর্ডার নির্বাচন করো']);
        }

        $sent = 0;
        $skipped = 0;
        $errors = [];

        foreach ($ids as $id) {
            $order = Order::find($id);

            if (! $order) {
                $errors[] = "#{$id}: পাওয়া যায়নি";
                continue;
            }
            if ($order->consignment_id) {
                $skipped++;
                continue;
            }

            try {
                $c = $steadfast->createConsignment($order);
                $order->update([
                    'status'         => 'sent_to_courier',
                    'consignment_id' => (string) ($c['consignment_id'] ?? ''),
                    'tracking_code'  => $c['tracking_code'] ?? null,
                ]);
                $sent++;
            } catch (\Throwable $e) {
                $errors[] = "#{$id}: ".$e->getMessage();
            }
        }

        $msg = "{$sent}টি পাঠানো হয়েছে".($skipped ? ", {$skipped}টি আগেই পাঠানো ছিল" : '');

        return $errors
            ? back()->with('status', $msg)->withErrors(['steadfast' => implode(' | ', $errors)])
            : back()->with('status', $msg);
    }

    /** কুরিয়ার লেবেল (৫০×৭৫mm) */
    public function labels(Request $request, SettingsService $settings)
    {
        $ids = array_filter(array_map('intval', explode(',', (string) $request->query('ids'))));

        // ইনপুটের ক্রম বজায় রাখি (লেবেল প্রিন্টের ক্রম)
        $found  = Order::whereIn('id', $ids)->get()->keyBy('id');
        $orders = collect($ids)->map(fn ($id) => $found->get($id))->filter()->values();

        return view('admin.labels', [
            'orders'   => $orders,
            'settings' => $settings->public(),
        ]);
    }
}

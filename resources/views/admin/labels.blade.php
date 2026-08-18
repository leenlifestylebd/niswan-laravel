<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>কুরিয়ার লেবেল — {{ $settings['brandName'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        /* ৫০×৭৫mm থার্মাল লেবেল */
        .label {
            width: 50mm; height: 75mm;
            padding: 3mm;
            box-sizing: border-box;
            border: 1px dashed #ccc;
            font-size: 8pt; line-height: 1.35;
            display: flex; flex-direction: column;
            background: #fff;
        }
        @media print { .label { border: none; } }
    </style>
</head>
<body class="bg-gray-100 font-[Hind_Siliguri]">

<div class="no-print sticky top-0 flex flex-wrap items-center gap-3 border-b border-gray-200 bg-white px-4 py-3">
    <span class="text-sm text-gray-600">{{ bn_num($orders->count()) }}টি লেবেল — ৫০×৭৫mm</span>
    <button onclick="window.print()" class="rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white">🖨️ প্রিন্ট</button>
    <button onclick="window.close()" class="rounded-full border border-gray-200 px-4 py-2 text-sm text-gray-600">বন্ধ করুন</button>
</div>

<div class="label-sheet flex flex-wrap gap-3 p-4">
    @forelse ($orders as $order)
        <div class="label">
            {{-- হেডার: মার্চেন্ট --}}
            <div style="border-bottom:1px solid #000; padding-bottom:1mm; margin-bottom:1.5mm;">
                <strong style="font-size:10pt;">{{ $settings['merchantName'] ?: $settings['brandName'] }}</strong>
                @if ($settings['merchantId'])
                    <div style="font-size:7pt;">মার্চেন্ট: {{ $settings['merchantId'] }}</div>
                @endif
                <div style="font-size:7pt;">{{ $settings['phone'] }}</div>
            </div>

            {{-- প্রাপক --}}
            <div style="flex:1;">
                <div><strong>প্রাপক:</strong> {{ $order->name }}</div>
                <div><strong>ফোন:</strong> {{ $order->phone }}</div>
                <div style="margin-top:1mm;">{{ $order->address }}</div>

                <div style="margin-top:1.5mm; border-top:1px dashed #999; padding-top:1mm;">
                    <div>{{ $order->product }}</div>
                    <div>সাইজ: {{ $order->size }} · পরিমাণ: {{ bn_num($order->qty) }}</div>
                </div>
            </div>

            {{-- ফুটার: COD + ট্র্যাকিং --}}
            <div style="border-top:1px solid #000; padding-top:1mm; margin-top:1.5mm;">
                <div style="font-size:11pt;"><strong>COD: {{ bdt($order->total) }}</strong></div>
                <div style="display:flex; justify-content:space-between; font-size:7pt;">
                    <span>ORD-{{ $order->id }}</span>
                    <span>{{ $order->tracking_code }}</span>
                </div>
            </div>
        </div>
    @empty
        <p class="p-8 text-gray-500">কোনো অর্ডার নির্বাচন করা হয়নি।</p>
    @endforelse
</div>

<script>
    // পেজ লোড হলেই প্রিন্ট ডায়ালগ
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
</script>
</body>
</html>

<section class="border-y border-gray-100 bg-white">
    <div class="mx-auto grid max-w-6xl grid-cols-2 gap-4 px-4 py-10 md:grid-cols-4">
        @foreach (config('store.features') as $f)
            <div class="flex flex-col items-center rounded-2xl p-4 text-center transition hover:bg-brand-light">
                <span class="text-3xl">{{ $f['icon'] }}</span>
                <h3 class="mt-3 font-semibold text-brand-dark">{{ $f['title'] }}</h3>
                <p class="mt-1 text-xs text-gray-500">{{ $f['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

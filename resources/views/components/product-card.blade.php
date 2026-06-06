<a href="{{ $link ?? '#' }}" class="group block bg-white border border-stone-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
    <div class="aspect-square bg-stone-100 overflow-hidden">
        @if($thumbnail ?? null)
            <img src="{{ $thumbnail }}" alt="{{ $name ?? 'Produk' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-stone-300">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </div>
    <div class="p-3">
        <h3 class="text-sm font-medium text-stone-900 line-clamp-2">{{ $name ?? 'Produk' }}</h3>
        <div class="mt-1">
            @include('components.price-display', [
                'price' => $minPrice ?? 0,
                'promoPrice' => $promoPrice ?? null,
                'hasPromotion' => $hasPromotion ?? false,
                'size' => 'sm',
            ])
        </div>
        @if(($rating ?? 0) > 0)
            <div class="mt-1">
                @include('components.star-rating', ['rating' => $rating ?? 0, 'size' => 'sm'])
            </div>
        @endif
    </div>
</a>

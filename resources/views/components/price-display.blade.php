@props([
    'price' => 0,
    'promoPrice' => null,
    'hasPromotion' => false,
    'size' => 'md',
])

@php
    $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
    $formattedPromo = $promoPrice ? 'Rp ' . number_format($promoPrice, 0, ',', '.') : null;
    $sizes = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];
    $priceClass = $sizes[$size] ?? 'text-base';
@endphp

<div class="flex flex-wrap items-baseline gap-x-1.5">
    @if($hasPromotion && $formattedPromo)
        <span class="font-semibold text-red-600 {{ $priceClass }}">{{ $formattedPromo }}</span>
        <span class="text-xs text-stone-400 line-through">{{ $formattedPrice }}</span>
    @else
        <span class="font-semibold text-stone-900 {{ $priceClass }}">{{ $formattedPrice }}</span>
    @endif
</div>

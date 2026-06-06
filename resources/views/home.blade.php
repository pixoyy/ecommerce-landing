@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-stone-900">Beranda</h1>
        <p class="text-stone-500 mt-2">EssenseLuxe — Toko fashion & kecantikan terpercaya.</p>

        <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @for ($i = 1; $i <= 8; $i++)
                @include('components.product-card', [
                    'link' => '#',
                    'name' => 'Produk Contoh ' . $i,
                    'minPrice' => rand(50000, 500000),
                    'hasPromotion' => $i % 3 === 0,
                    'promoPrice' => $i % 3 === 0 ? rand(40000, 450000) : null,
                    'rating' => rand(30, 50) / 10,
                ])
            @endfor
        </div>
    </div>
@endsection

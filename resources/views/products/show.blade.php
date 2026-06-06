@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="aspect-square bg-stone-100 rounded-xl"></div>
            <div>
                <h1 class="text-2xl font-bold text-stone-900" id="product-name">Memuat...</h1>
                <div id="variant-selector" class="mt-4"></div>
            </div>
        </div>
    </div>
@endsection

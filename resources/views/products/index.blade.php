@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-stone-900">Produk</h1>
        <p class="text-stone-500 mt-1">Jelajahi koleksi produk kami.</p>
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="product-grid">
            {{-- Products loaded by Alpine.js --}}
        </div>
    </div>
@endsection

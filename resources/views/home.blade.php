@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div x-data="homePage()" x-init="init()">
    {{-- Hero Banner --}}
    <section class="bg-stone-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight">EssenseLuxe</h1>
            <p class="mt-4 text-lg text-stone-300 max-w-2xl mx-auto">Temukan koleksi fashion & kecantikan terbaik untuk gaya Anda.</p>
            <a href="/products" class="inline-block mt-6 bg-white text-stone-900 px-8 py-3 rounded-lg font-medium hover:bg-stone-100 transition-colors">Belanja Sekarang</a>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        {{-- Categories --}}
        <section>
            <h2 class="text-xl font-bold text-stone-900 mb-6">Kategori</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <template x-for="category in categories" :key="category.id">
                    <a :href="'/products?category=' + category.slug"
                       class="group relative aspect-square bg-stone-100 rounded-xl overflow-hidden border border-stone-200 hover:shadow-lg transition-shadow">
                        <div class="absolute inset-0 flex items-center justify-center bg-stone-900/0 group-hover:bg-stone-900/10 transition-colors">
                            <span class="text-sm font-medium text-stone-900" x-text="category.name"></span>
                        </div>
                    </a>
                </template>
            </div>
        </section>

        {{-- Brands --}}
        <section>
            <h2 class="text-xl font-bold text-stone-900 mb-6">Merek</h2>
            <div class="flex flex-wrap gap-3">
                <template x-for="brand in brands" :key="brand.id">
                    <a :href="'/products?brand=' + brand.slug"
                       class="px-5 py-2.5 bg-white border border-stone-200 rounded-full text-sm font-medium text-stone-600 hover:border-stone-900 hover:text-stone-900 transition-colors"
                       x-text="brand.name">
                    </a>
                </template>
            </div>
        </section>

        {{-- New Products --}}
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-stone-900">Produk Terbaru</h2>
                <a href="/products" class="text-sm font-medium text-stone-600 hover:text-stone-900 transition-colors">Lihat Semua</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="product in latestProducts" :key="product.id">
                    <a :href="'/products/' + product.slug" class="group block bg-white border border-stone-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-square bg-stone-100 overflow-hidden">
                            <template x-if="product.thumbnail">
                                <img :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </template>
                            <template x-if="!product.thumbnail">
                                <div class="w-full h-full flex items-center justify-center text-stone-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-medium text-stone-900 line-clamp-2" x-text="product.name"></h3>
                            <div class="mt-1">
                                <template x-if="product.has_active_promotion && product.promo_price">
                                    <div class="flex items-baseline gap-1.5">
                                        <span class="font-semibold text-red-600 text-sm" x-text="'Rp ' + Number(product.promo_price).toLocaleString('id-ID')"></span>
                                        <span class="text-xs text-stone-400 line-through" x-text="'Rp ' + Number(product.min_price).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                                <template x-if="!product.has_active_promotion || !product.promo_price">
                                    <span class="font-semibold text-stone-900 text-sm" x-text="'Rp ' + Number(product.min_price).toLocaleString('id-ID')"></span>
                                </template>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
            <div x-show="!latestProducts.length && !isLoading" class="text-center py-12 text-stone-400">
                Belum ada produk tersedia.
            </div>
        </section>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('homePage', () => ({
                    categories: [],
                    brands: [],
                    latestProducts: [],
                    isLoading: true,

                    async init() {
                        try {
                            const [catRes, brandRes, prodRes] = await Promise.all([
                                apiClient.get('/categories', { params: { per_page: 5 } }),
                                apiClient.get('/brands', { params: { per_page: 10 } }),
                                apiClient.get('/products', { params: { sort: 'newest', per_page: 8 } }),
                            ]);
                            this.categories = catRes.data.data || [];
                            this.brands = brandRes.data.data || [];
                            this.latestProducts = prodRes.data.data || [];
                        } catch (err) {
                            console.error('Failed to load home page data', err);
                        } finally {
                            this.isLoading = false;
                        }
                    },
                }));
            });
        </script>
    @endpush
@endonce
@endsection

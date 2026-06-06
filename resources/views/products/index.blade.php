@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div x-data="productList()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Filter Sidebar --}}
        <aside class="lg:w-56 shrink-0">
            <div class="lg:sticky lg:top-8 space-y-6">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Cari</label>
                    <input type="search" x-model="filters.search" @input.debounce.300ms="loadProducts()"
                           placeholder="Cari produk..."
                           class="w-full px-4 py-2 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
                </div>

                {{-- Categories --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Kategori</label>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto">
                        <template x-for="cat in categories" :key="cat.id">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :value="cat.slug" x-model="filters.category"
                                       @change="loadProducts()" class="rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                                <span class="text-sm text-stone-600" x-text="cat.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Brands --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Merek</label>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto">
                        <template x-for="brand in brands" :key="brand.id">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :value="brand.slug" x-model="filters.brand"
                                       @change="loadProducts()" class="rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                                <span class="text-sm text-stone-600" x-text="brand.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Gender</label>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" x-model="filters.gender" :value="null"
                                   @change="loadProducts()" class="border-stone-300 text-stone-900 focus:ring-stone-900">
                            <span class="text-sm text-stone-600">Semua</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" x-model="filters.gender" value="1"
                                   @change="loadProducts()" class="border-stone-300 text-stone-900 focus:ring-stone-900">
                            <span class="text-sm text-stone-600">Pria</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" x-model="filters.gender" value="2"
                                   @change="loadProducts()" class="border-stone-300 text-stone-900 focus:ring-stone-900">
                            <span class="text-sm text-stone-600">Wanita</span>
                        </label>
                    </div>
                </div>

                {{-- Price Range --}}
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Rentang Harga</label>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="filters.price_min" @input.debounce.500ms="loadProducts()"
                               placeholder="Min"
                               class="w-full px-3 py-2 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
                        <span class="text-stone-400">-</span>
                        <input type="number" x-model="filters.price_max" @input.debounce.500ms="loadProducts()"
                               placeholder="Max"
                               class="w-full px-3 py-2 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <section class="flex-1 min-w-0">
            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-stone-500">
                    <span x-text="meta.total || 0"></span> produk ditemukan
                </p>
                <select x-model="filters.sort" @change="loadProducts()"
                        class="px-3 py-2 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
                    <option value="newest">Terbaru</option>
                    <option value="price_asc">Termurah</option>
                    <option value="price_desc">Termahal</option>
                </select>
            </div>

            {{-- Loading --}}
            <div x-show="isLoading" class="flex justify-center py-12">
                @include('components.loading-spinner', ['size' => 'lg'])
            </div>

            {{-- Product Grid --}}
            <div x-show="!isLoading" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="product in products" :key="product.id">
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
                            <div class="mt-1 flex items-center gap-1" x-show="product.average_rating > 0">
                                <span class="text-amber-400 text-xs" x-text="'★'.repeat(Math.round(product.average_rating))"></span>
                                <span class="text-xs text-stone-400" x-text="'(' + product.review_count + ')'"></span>
                            </div>
                        </div>
                    </a>
                </template>
            </div>

            {{-- Empty State --}}
            <div x-show="!isLoading && products.length === 0" class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-4 text-stone-500 font-medium">Tidak ada produk ditemukan</p>
                <p class="text-sm text-stone-400 mt-1">Coba ubah filter atau kata kunci pencarian Anda.</p>
                <button @click="resetFilters()" class="mt-4 text-sm text-stone-900 underline hover:no-underline">Reset Filter</button>
            </div>

            {{-- Pagination --}}
            <div x-show="meta.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
                <button @click="goToPage(meta.current_page - 1)" :disabled="meta.current_page === 1"
                        class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Sebelumnya
                </button>
                <template x-for="page in pages()" :key="page">
                    <button @click="goToPage(page)"
                            class="px-3 py-2 text-sm border rounded-lg transition-colors"
                            :class="page === meta.current_page ? 'bg-stone-900 text-white border-stone-900' : 'border-stone-300 hover:bg-stone-50'"
                            x-text="page">
                    </button>
                </template>
                <button @click="goToPage(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page"
                        class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Berikutnya
                </button>
            </div>
        </section>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productList', () => ({
                    products: [],
                    categories: [],
                    brands: [],
                    meta: { current_page: 1, last_page: 1, per_page: 12, total: 0 },
                    isLoading: true,
                    filters: {
                        category: [],
                        brand: [],
                        gender: null,
                        search: '',
                        price_min: '',
                        price_max: '',
                        sort: 'newest',
                    },

                    async init() {
                        await Promise.all([this.loadCategories(), this.loadBrands()]);
                        await this.loadProducts();
                    },

                    buildParams() {
                        const params = { page: this.meta.current_page, per_page: 12 };
                        if (this.filters.category.length) params.category = this.filters.category.join(',');
                        if (this.filters.brand.length) params.brand = this.filters.brand.join(',');
                        if (this.filters.gender) params.gender = this.filters.gender;
                        if (this.filters.search) params.search = this.filters.search;
                        if (this.filters.price_min) params.price_min = this.filters.price_min;
                        if (this.filters.price_max) params.price_max = this.filters.price_max;
                        if (this.filters.sort) params.sort = this.filters.sort;
                        return params;
                    },

                    async loadCategories() {
                        try {
                            const res = await apiClient.get('/categories', { params: { per_page: 50 } });
                            this.categories = res.data.data || [];
                        } catch (e) { console.error('Failed to load categories', e); }
                    },

                    async loadBrands() {
                        try {
                            const res = await apiClient.get('/brands', { params: { per_page: 50 } });
                            this.brands = res.data.data || [];
                        } catch (e) { console.error('Failed to load brands', e); }
                    },

                    async loadProducts() {
                        this.isLoading = true;
                        try {
                            const res = await apiClient.get('/products', { params: this.buildParams() });
                            this.products = res.data.data || [];
                            this.meta = res.data.meta || { current_page: 1, last_page: 1, per_page: 12, total: 0 };
                        } catch (e) {
                            console.error('Failed to load products', e);
                            this.products = [];
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async goToPage(page) {
                        if (page < 1 || page > this.meta.last_page) return;
                        this.meta.current_page = page;
                        await this.loadProducts();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },

                    pages() {
                        const total = this.meta.last_page;
                        const current = this.meta.current_page;
                        const delta = 2;
                        const range = [];
                        for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
                            range.push(i);
                        }
                        if (current - delta > 2) range.unshift('...');
                        if (current + delta < total - 1) range.push('...');
                        range.unshift(1);
                        if (total > 1) range.push(total);
                        return range;
                    },

                    resetFilters() {
                        this.filters = { category: [], brand: [], gender: null, search: '', price_min: '', price_max: '', sort: 'newest' };
                        this.meta.current_page = 1;
                        this.loadProducts();
                    },
                }));
            });
        </script>
    @endpush
@endonce
@endsection

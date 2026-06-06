@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div x-data="productDetail()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div x-show="!product && !error && !isLoading" class="text-center py-16">
        @include('components.loading-spinner', ['size' => 'lg'])
    </div>

    <div x-show="error" class="text-center py-16">
        <p class="text-stone-500" x-text="error"></p>
        <a href="/products" class="mt-4 inline-block text-sm text-stone-900 underline">Kembali ke produk</a>
    </div>

    <template x-if="product">
        <div>
            {{-- Breadcrumb --}}
            <nav class="text-sm text-stone-500 mb-6">
                <a href="/" class="hover:text-stone-900">Beranda</a>
                <span class="mx-2">/</span>
                <a href="/products" class="hover:text-stone-900">Produk</a>
                <span class="mx-2">/</span>
                <span class="text-stone-900" x-text="product.name"></span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                {{-- Left: Gallery --}}
                <div>
                         <div class="aspect-square bg-stone-100 rounded-xl overflow-hidden">
                            <img :src="selectedImage" :alt="product.name" loading="lazy"
                                 class="w-full h-full object-cover cursor-pointer" @click="openLightbox = true">
                        </div>
                    <div class="mt-3 flex gap-2 overflow-x-auto" x-show="(product.images || []).length > 0">
                        <template x-for="(img, idx) in product.images" :key="img.id">
                            <button @click="selectedImageIndex = idx"
                                    class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-colors"
                                    :class="idx === selectedImageIndex ? 'border-stone-900' : 'border-transparent'">
                                <img :src="img.url" :alt="'Gambar ' + (idx + 1)" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>

                    {{-- Lightbox --}}
                    <div x-show="openLightbox" @click="openLightbox = false"
                         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
                        <img :src="selectedImage" :alt="product.name" class="max-w-full max-h-full object-contain">
                    </div>
                </div>

                {{-- Right: Info --}}
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-stone-900" x-text="product.name"></h1>

                    <div class="mt-2 flex items-center gap-3">
                        <div class="flex items-center gap-1">
                            <span class="text-amber-400" x-html="renderStars(product.average_rating)"></span>
                            <span class="text-sm text-stone-500 ml-1" x-text="product.average_rating?.toFixed(1)"></span>
                        </div>
                        <span class="text-sm text-stone-400" x-text="'(' + (product.total_reviews || 0) + ' ulasan)'"></span>
                    </div>

                    {{-- Price --}}
                    <div class="mt-6" x-show="selectedVariant">
                        <template x-if="selectedVariant.promo_price">
                            <div class="flex items-baseline gap-3">
                                <span class="text-2xl font-bold text-red-600" x-text="formatPrice(selectedVariant.promo_price)"></span>
                                <span class="text-lg text-stone-400 line-through" x-text="formatPrice(selectedVariant.price)"></span>
                                <span class="text-xs font-medium text-white bg-red-500 px-2 py-0.5 rounded-full">Diskon</span>
                            </div>
                        </template>
                        <template x-if="!selectedVariant.promo_price">
                            <span class="text-2xl font-bold text-stone-900" x-text="formatPrice(selectedVariant.price)"></span>
                        </template>
                    </div>

                    {{-- Variant Selector --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-stone-700 mb-2">Varian</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="variant in (product.variants || [])" :key="variant.id">
                                <button @click="selectVariant(variant)"
                                        class="px-4 py-2.5 text-sm border-2 rounded-lg transition-colors"
                                        :class="selectedVariant?.id === variant.id
                                            ? 'border-stone-900 bg-stone-900 text-white'
                                            : 'border-stone-200 hover:border-stone-400'">
                                    <span x-text="variant.label"></span>
                                    <span class="ml-2 text-xs opacity-75" x-text="variant.promo_price ? formatPrice(variant.promo_price) : formatPrice(variant.price)"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Stock Info --}}
                    <p class="mt-3 text-sm" x-show="selectedVariant">
                        <span x-text="selectedVariant.stock > 0 ? 'Stok: ' + selectedVariant.stock : 'Stok habis'"
                              :class="selectedVariant.stock > 0 ? 'text-emerald-600' : 'text-red-600'"></span>
                    </p>

                    {{-- Quantity + Add to Cart --}}
                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex items-center border border-stone-300 rounded-lg">
                            <button @click="decrementQty()" class="px-3 py-2 text-stone-600 hover:bg-stone-50 transition-colors">−</button>
                            <span class="px-4 py-2 text-sm font-medium min-w-[3rem] text-center" x-text="quantity"></span>
                            <button @click="incrementQty()" class="px-3 py-2 text-stone-600 hover:bg-stone-50 transition-colors">+</button>
                        </div>
                        <button @click="addToCart()" :disabled="!selectedVariant || isLoading"
                                class="flex-1 bg-stone-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isLoading">Tambah ke Keranjang</span>
                            <span x-show="isLoading">Memproses...</span>
                        </button>
                    </div>

                    {{-- Success Message --}}
                    <div x-show="successMessage" x-transition
                         class="mt-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2.5 rounded-lg text-sm">
                        <span x-text="successMessage"></span>
                    </div>

                    {{-- Description --}}
                    <div class="mt-8 border-t border-stone-200 pt-6">
                        <h2 class="text-lg font-semibold text-stone-900 mb-3">Deskripsi</h2>
                        <div class="text-stone-600 text-sm leading-relaxed prose prose-sm max-w-none" x-html="product.description || 'Tidak ada deskripsi.'"></div>
                    </div>

                    <div class="mt-6 border-t border-stone-200 pt-6" x-show="product.features">
                        <h2 class="text-lg font-semibold text-stone-900 mb-3">Fitur</h2>
                        <div class="text-stone-600 text-sm leading-relaxed prose prose-sm max-w-none" x-html="product.features"></div>
                    </div>
                </div>
            </div>

            {{-- Reviews Section --}}
            <div class="mt-12 border-t border-stone-200 pt-8">
                <h2 class="text-lg font-semibold text-stone-900 mb-6">Ulasan</h2>

                <div class="flex items-center gap-4 mb-6" x-show="product.average_rating > 0">
                    <div class="text-3xl font-bold text-stone-900" x-text="product.average_rating?.toFixed(1)"></div>
                    <div>
                        <div class="flex items-center gap-1" x-html="renderStars(product.average_rating)"></div>
                        <p class="text-sm text-stone-500 mt-0.5" x-text="(product.total_reviews || 0) + ' ulasan'"></p>
                    </div>
                </div>

                <div x-show="reviews.length === 0 && !reviewsLoading" class="text-center py-8 text-stone-400">
                    Belum ada ulasan untuk produk ini.
                </div>

                <div class="space-y-4">
                    <template x-for="review in reviews" :key="review.id">
                        <div class="bg-white border border-stone-200 rounded-xl p-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-stone-900" x-text="review.user_name || 'Anonymous'"></span>
                                <span class="text-xs text-stone-400" x-text="review.created_at || ''"></span>
                            </div>
                            <div class="mt-1" x-html="renderStars(review.rating)"></div>
                            <p class="mt-2 text-sm text-stone-600" x-show="review.review" x-text="review.review"></p>
                        </div>
                    </template>
                </div>

                <div x-show="reviewsMeta.last_page > 1" class="mt-6 flex justify-center gap-2">
                    <button @click="loadReviews(reviewsMeta.current_page - 1)" :disabled="reviewsMeta.current_page === 1"
                            class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50">
                        Sebelumnya
                    </button>
                    <button @click="loadReviews(reviewsMeta.current_page + 1)" :disabled="reviewsMeta.current_page === reviewsMeta.last_page"
                            class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50">
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productDetail', () => ({
                    product: null,
                    selectedVariant: null,
                    selectedImageIndex: 0,
                    quantity: 1,
                    isLoading: false,
                    error: null,
                    successMessage: '',
                    openLightbox: false,
                    reviews: [],
                    reviewsMeta: { current_page: 1, last_page: 1, total: 0 },
                    reviewsLoading: false,

                    get selectedImage() {
                        if (!this.product) return '';
                        const images = this.product.images || [];
                        if (images.length > 0 && images[this.selectedImageIndex]) {
                            return images[this.selectedImageIndex].url;
                        }
                        return this.product.thumbnail || '';
                    },

                    async init() {
                        const slug = window.location.pathname.split('/').pop();
                        await this.loadProduct(slug);
                    },

                    async loadProduct(slug) {
                        this.isLoading = true;
                        this.error = null;
                        try {
                            const res = await apiClient.get('/products/' + slug);
                            this.product = res.data.data;
                            if (this.product.variants && this.product.variants.length > 0) {
                                this.selectVariant(this.product.variants[0]);
                            }
                            await this.loadReviews(1);
                        } catch (err) {
                            if (err.response?.status === 404) {
                                this.error = 'Produk tidak ditemukan.';
                            } else {
                                this.error = 'Terjadi kesalahan saat memuat produk.';
                            }
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    selectVariant(variant) {
                        this.selectedVariant = variant;
                        this.quantity = 1;
                    },

                    incrementQty() {
                        if (this.selectedVariant && this.quantity < Math.min(this.selectedVariant.stock || 100, 100)) {
                            this.quantity++;
                        }
                    },

                    decrementQty() {
                        if (this.quantity > 1) this.quantity--;
                    },

                    async addToCart() {
                        if (!this.selectedVariant) return;
                        if (!Alpine.store('auth').isAuthenticated) {
                            window.location.href = '/login';
                            return;
                        }
                        this.isLoading = true;
                        this.successMessage = '';
                        try {
                            const res = await apiClient.post('/cart/items', {
                                product_variant_id: this.selectedVariant.id,
                                quantity: this.quantity,
                            });
                            Alpine.store('cart').addItem(res.data.data);
                            this.successMessage = 'Berhasil ditambahkan ke keranjang!';
                            setTimeout(() => { this.successMessage = ''; }, 3000);
                        } catch (err) {
                            const msg = err.response?.data?.message || 'Gagal menambahkan ke keranjang.';
                            alert(msg);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async loadReviews(page = 1) {
                        if (!this.product) return;
                        this.reviewsLoading = true;
                        try {
                            const res = await apiClient.get('/products/' + this.product.id + '/reviews', {
                                params: { page, per_page: 5 }
                            });
                            this.reviews = res.data.data || [];
                            this.reviewsMeta = res.data.meta || { current_page: 1, last_page: 1, total: 0 };
                        } catch (e) {
                            this.reviews = [];
                        } finally {
                            this.reviewsLoading = false;
                        }
                    },

                    formatPrice(price) {
                        if (!price) return '';
                        return 'Rp ' + Number(price).toLocaleString('id-ID');
                    },

                    renderStars(rating) {
                        if (!rating) return '';
                        const count = Math.round(rating);
                        return '★'.repeat(count) + '☆'.repeat(5 - count);
                    },
                }));
            });
        </script>
    @endpush
@endonce
@endsection

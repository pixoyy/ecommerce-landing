@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
    <div x-data="cartPage()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-stone-900">Keranjang</h1>

        {{-- Loading --}}
        <div x-show="isLoading && items.length === 0" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        {{-- Error --}}
        <div x-show="error" class="text-center py-16">
            <p class="text-stone-500" x-text="error"></p>
            <button @click="loadCart()" class="mt-4 text-sm text-stone-900 underline">Coba lagi</button>
        </div>

        {{-- Empty cart --}}
        <div x-show="!isLoading && !error && items.length === 0" class="text-center py-16">
            <svg class="mx-auto w-20 h-20 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <h2 class="mt-4 text-lg font-semibold text-stone-900">Keranjang belanja masih kosong</h2>
            <p class="mt-1 text-sm text-stone-500">Tambahkan produk favorit Anda ke keranjang</p>
            <a href="/products" class="mt-6 inline-block bg-stone-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors">
                Mulai Belanja
            </a>
        </div>

        {{-- Cart items + summary --}}
        <div x-show="!isLoading && !error && items.length > 0" class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart items list --}}
            <div class="lg:col-span-2 space-y-4">
                <template x-for="item in items" :key="item.id">
                    <div class="bg-white border border-stone-200 rounded-xl p-4 flex gap-4">
                        {{-- Thumbnail --}}
                        <div class="w-20 h-20 shrink-0 bg-stone-100 rounded-lg overflow-hidden">
                            <img :src="item.product.thumbnail || item.product.image" :alt="item.product.name" class="w-full h-full object-cover">
                        </div>

                        {{-- Info + quantity --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-stone-900 truncate" x-text="item.product.name"></h3>
                            <p class="text-sm text-stone-500 mt-0.5" x-text="item.variant_label"></p>

                            {{-- Price --}}
                            <div class="mt-1">
                                <template x-if="item.promo_price">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-red-600" x-text="formatPrice(item.promo_price)"></span>
                                        <span class="text-xs text-stone-400 line-through" x-text="formatPrice(item.price)"></span>
                                    </div>
                                </template>
                                <template x-if="!item.promo_price">
                                    <span class="text-sm font-semibold text-stone-900" x-text="formatPrice(item.price)"></span>
                                </template>
                            </div>

                            {{-- Stock warning --}}
                            <p x-show="item.is_stock_sufficient === false" class="mt-1 text-xs text-red-600">
                                Stok tidak mencukupi. Tersedia: <span x-text="item.available_stock || 0"></span>
                            </p>

                            {{-- Quantity stepper --}}
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-stone-500">Qty:</span>
                                <div class="flex items-center border border-stone-300 rounded-lg">
                                    <button @click="decrementQty(item)" :disabled="item.quantity <= 1"
                                            class="px-2 py-1 text-stone-600 hover:bg-stone-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                        −
                                    </button>
                                    <input type="number" :value="item.quantity"
                                           @input.debounce.750ms="updateQuantity(item, $event.target.value)"
                                           min="1" max="100"
                                           class="w-12 text-center text-sm py-1 border-x border-stone-300 focus:outline-none">
                                    <button @click="incrementQty(item)" :disabled="item.quantity >= maxQty(item)"
                                            class="px-2 py-1 text-stone-600 hover:bg-stone-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                        +
                                    </button>
                                </div>
                                <span x-show="updatingItems[item.id]" class="inline-block w-4 h-4 border-2 border-stone-300 border-t-stone-900 rounded-full animate-spin"></span>
                            </div>
                        </div>

                        {{-- Remove + subtotal --}}
                        <div class="flex flex-col items-end justify-between shrink-0">
                            <button @click="removeItem(item.id)" class="p-1 text-stone-400 hover:text-red-600 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            <span class="text-sm font-semibold text-stone-900" x-text="formatPrice(item.subtotal)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Cart summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-stone-200 rounded-xl p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Ringkasan</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-stone-600">
                            <span>Total item</span>
                            <span class="font-medium text-stone-900" x-text="totalItemCount + ' barang'"></span>
                        </div>
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-stone-900" x-text="formatPrice(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-stone-600">
                            <span>Estimasi ongkir</span>
                            <span class="font-medium text-stone-400">Dihitung nanti</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-stone-200">
                        <div class="flex justify-between font-semibold text-stone-900">
                            <span>Total</span>
                            <span x-text="formatPrice(subtotal)"></span>
                        </div>
                    </div>
                    <a href="/checkout" class="mt-4 w-full block text-center bg-stone-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors">
                        Checkout
                    </a>
                    <a href="/products" class="mt-2 w-full block text-center text-sm text-stone-500 hover:text-stone-900 transition-colors">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Remove confirmation modal --}}
    <div x-show="removeModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/40" @click="removeModalOpen = false"></div>
        <div class="relative bg-white rounded-xl p-6 max-w-sm w-full shadow-xl">
            <h3 class="text-lg font-semibold text-stone-900">Hapus item ini?</h3>
            <p class="mt-1 text-sm text-stone-500">Item akan dihapus dari keranjang Anda.</p>
            <div class="mt-4 flex gap-3 justify-end">
                <button @click="removeModalOpen = false" class="px-4 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg transition-colors">Batal</button>
                <button @click="confirmRemove()" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors" x-text="isRemoving ? 'Menghapus...' : 'Ya, Hapus'" :disabled="isRemoving"></button>
            </div>
        </div>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('cartPage', () => ({
                        items: [],
                        isLoading: false,
                        error: null,
                        removeModalOpen: false,
                        removeTargetId: null,
                        isRemoving: false,
                        updatingItems: {},

                        get totalItemCount() {
                            return this.items.reduce((sum, i) => sum + i.quantity, 0);
                        },

                        get subtotal() {
                            return this.items.reduce((sum, i) => sum + (i.subtotal || 0), 0);
                        },

                        async init() {
                            await this.loadCart();
                        },

                        async loadCart() {
                            this.isLoading = true;
                            this.error = null;
                            try {
                                const res = await apiClient.get('/cart');
                                this.items = res.data.data || [];
                                Alpine.store('cart').setItems(this.items);
                            } catch (err) {
                                if (err.response?.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                this.error = 'Gagal memuat keranjang. Silakan coba lagi.';
                            } finally {
                                this.isLoading = false;
                            }
                        },

                        maxQty(item) {
                            const stockLimit = item.available_stock || item.stock || 100;
                            return Math.min(stockLimit, 100);
                        },

                        decrementQty(item) {
                            if (item.quantity > 1) {
                                item.quantity--;
                                this.scheduleUpdate(item);
                            }
                        },

                        incrementQty(item) {
                            const max = this.maxQty(item);
                            if (item.quantity < max) {
                                item.quantity++;
                                this.scheduleUpdate(item);
                            }
                        },

                        scheduleUpdate(item) {
                            if (this._updateTimer) clearTimeout(this._updateTimer);
                            this._updateTimer = setTimeout(() => {
                                this.updateQuantity(item, item.quantity);
                            }, 600);
                        },

                        async updateQuantity(item, newQty) {
                            let qty = parseInt(newQty);
                            if (isNaN(qty) || qty < 1) qty = 1;
                            const max = this.maxQty(item);
                            if (qty > max) qty = max;
                            item.quantity = qty;
                            this.updatingItems[item.id] = true;
                            try {
                                const res = await apiClient.put('/cart/items/' + item.id, { quantity: qty });
                                const data = res.data.data || {};
                                item.subtotal = data.subtotal || (item.price * qty);
                                item.promo_price = data.promo_price || item.promo_price;
                                item.price = data.price || item.price;
                                item.is_stock_sufficient = data.is_stock_sufficient;
                                Alpine.store('cart').updateItem(item.id, qty, item.subtotal);
                            } catch (err) {
                                const msg = err.response?.data?.message || 'Gagal memperbarui jumlah.';
                                alert(msg);
                                await this.loadCart();
                            } finally {
                                this.updatingItems[item.id] = false;
                            }
                        },

                        removeItem(itemId) {
                            this.removeTargetId = itemId;
                            this.removeModalOpen = true;
                        },

                        async confirmRemove() {
                            if (!this.removeTargetId) return;
                            this.isRemoving = true;
                            try {
                                await apiClient.delete('/cart/items/' + this.removeTargetId);
                                this.items = this.items.filter(i => i.id !== this.removeTargetId);
                                Alpine.store('cart').removeItem(this.removeTargetId);
                                this.removeModalOpen = false;
                                this.removeTargetId = null;
                            } catch (err) {
                                alert('Gagal menghapus item.');
                            } finally {
                                this.isRemoving = false;
                            }
                        },

                        formatPrice(price) {
                            if (!price) return '';
                            return 'Rp ' + Number(price).toLocaleString('id-ID');
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
@endsection

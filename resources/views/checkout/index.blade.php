@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div x-data="checkoutForm()" x-init="init()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-stone-900">Checkout</h1>

        {{-- Loading --}}
        <div x-show="isLoading && !error" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        {{-- Error --}}
        <div x-show="error && !isLoading" class="text-center py-16">
            <p class="text-stone-500" x-text="error"></p>
            <button @click="init()" class="mt-4 text-sm text-stone-900 underline">Coba lagi</button>
        </div>

        {{-- Checkout form --}}
        <div x-show="!isLoading && cartItems.length > 0" class="mt-8 grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- Left: Shipping form + point redemption --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Shipping form --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Informasi Pengiriman</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">Nama Penerima</label>
                                <input type="text" x-model="form.buyer_name"
                                       class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                       :class="errors.buyer_name ? 'border-red-500' : ''">
                                <p x-show="errors.buyer_name" class="mt-1 text-xs text-red-600" x-text="errors.buyer_name"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">Email</label>
                                <input type="email" x-model="form.buyer_email"
                                       class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                       :class="errors.buyer_email ? 'border-red-500' : ''">
                                <p x-show="errors.buyer_email" class="mt-1 text-xs text-red-600" x-text="errors.buyer_email"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">No. Telepon</label>
                            <input type="tel" x-model="form.buyer_phone"
                                   class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                   :class="errors.buyer_phone ? 'border-red-500' : ''">
                            <p x-show="errors.buyer_phone" class="mt-1 text-xs text-red-600" x-text="errors.buyer_phone"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Alamat Pengiriman <span class="text-red-500">*</span></label>
                            <textarea x-model="form.shipping_address" rows="3"
                                      class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                      :class="errors.shipping_address ? 'border-red-500' : ''"></textarea>
                            <p x-show="errors.shipping_address" class="mt-1 text-xs text-red-600" x-text="errors.shipping_address"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Catatan Pengiriman (opsional)</label>
                            <textarea x-model="form.shipping_note" rows="2"
                                      class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Point redemption --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Poin Reward</h2>
                    <p class="text-sm text-stone-600 mb-3">
                        Saldo poin Anda: <span class="font-semibold text-stone-900" x-text="formatPrice(pointBalance)"></span>
                    </p>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Gunakan Poin</label>
                        <div class="flex items-center gap-3">
                            <input type="number" x-model="redeemPoints" min="0" :max="maxRedeemable"
                                   @input="validatePoints()"
                                   class="w-36 px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
                            <span class="text-xs text-stone-500">Maks: <span x-text="formatPrice(maxRedeemable)"></span></span>
                        </div>
                        <p x-show="redeemPoints > 0" class="mt-1 text-xs text-emerald-600">
                            Diskon poin: -<span x-text="formatPrice(redeemPoints)"></span>
                        </p>
                        <p x-show="pointError" class="mt-1 text-xs text-red-600" x-text="pointError"></p>
                    </div>
                </div>
            </div>

            {{-- Right: Order summary + price breakdown --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3">
                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex gap-3">
                                <div class="w-12 h-12 shrink-0 bg-stone-100 rounded-lg overflow-hidden">
                                    <img :src="item.product.thumbnail || item.product.image" :alt="item.product.name" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-stone-900 truncate" x-text="item.product.name"></p>
                                    <p class="text-xs text-stone-500" x-text="item.variant_label"></p>
                                    <p class="text-xs text-stone-500" x-text="item.quantity + ' x ' + formatPrice(item.promo_price || item.price)"></p>
                                </div>
                                <span class="text-sm font-medium text-stone-900 shrink-0" x-text="formatPrice(item.subtotal)"></span>
                            </div>
                        </template>
                    </div>
                    <a href="/cart" class="mt-3 inline-block text-xs text-stone-500 hover:text-stone-900 underline">Ubah Pesanan</a>
                </div>

                {{-- Price breakdown --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Rincian Biaya</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-stone-900" x-text="formatPrice(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-stone-600">
                            <span>Ongkos Kirim</span>
                            <span class="font-medium text-stone-900" x-text="formatPrice(shippingCost)"></span>
                        </div>
                        <div x-show="redeemPoints > 0" class="flex justify-between text-emerald-600">
                            <span>Diskon Poin</span>
                            <span class="font-medium">-<span x-text="formatPrice(redeemPoints)"></span></span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-stone-200">
                        <div class="flex justify-between font-semibold text-stone-900 text-base">
                            <span>Total</span>
                            <span x-text="formatPrice(total)"></span>
                        </div>
                    </div>

                    {{-- Place order button --}}
                    <button @click="placeOrder()" :disabled="isSubmitting || !form.shipping_address"
                            class="mt-4 w-full bg-stone-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Buat Pesanan</span>
                        <span x-show="isSubmitting">Memproses...</span>
                    </button>

                    {{-- Submit error --}}
                    <div x-show="submitError" class="mt-3 bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-lg text-sm" x-text="submitError"></div>
                </div>
            </div>
        </div>

        {{-- Empty cart --}}
        <div x-show="!isLoading && !error && cartItems.length === 0" class="text-center py-16">
            <p class="text-stone-500">Keranjang belanja kosong. Silakan tambahkan produk terlebih dahulu.</p>
            <a href="/products" class="mt-4 inline-block text-sm text-stone-900 underline">Mulai Belanja</a>
        </div>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('checkoutForm', () => ({
                        cartItems: [],
                        isLoading: false,
                        error: null,
                        isSubmitting: false,
                        submitError: null,
                        pointBalance: 0,
                        redeemPoints: 0,
                        pointError: null,
                        shippingCost: 15000,
                        form: {
                            buyer_name: '',
                            buyer_email: '',
                            buyer_phone: '',
                            shipping_address: '',
                            shipping_note: '',
                        },
                        errors: {},

                        get subtotal() {
                            return this.cartItems.reduce((sum, i) => sum + (i.subtotal || 0), 0);
                        },

                        get maxRedeemable() {
                            return Math.min(this.pointBalance, this.subtotal);
                        },

                        get total() {
                            return Math.max(0, this.subtotal + this.shippingCost - (this.redeemPoints || 0));
                        },

                        async init() {
                            if (!Alpine.store('auth').isAuthenticated) {
                                window.location.href = '/login';
                                return;
                            }
                            const user = Alpine.store('auth').user || {};
                            this.form.buyer_name = user.name || '';
                            this.form.buyer_email = user.email || '';
                            this.form.buyer_phone = user.phone || '';
                            await Promise.all([this.loadCart(), this.loadPointBalance()]);
                        },

                        async loadCart() {
                            this.isLoading = true;
                            this.error = null;
                            try {
                                const res = await apiClient.get('/cart');
                                this.cartItems = res.data.data || [];
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

                        async loadPointBalance() {
                            try {
                                const res = await apiClient.get('/rewards/balance');
                                this.pointBalance = res.data.data?.balance || 0;
                            } catch {
                                this.pointBalance = 0;
                            }
                        },

                        validatePoints() {
                            this.pointError = null;
                            if (this.redeemPoints < 0) this.redeemPoints = 0;
                            if (this.redeemPoints > this.pointBalance) {
                                this.pointError = 'Poin tidak boleh melebihi saldo.';
                                this.redeemPoints = this.pointBalance;
                            }
                            if (this.redeemPoints > this.subtotal) {
                                this.pointError = 'Poin tidak boleh melebihi subtotal.';
                                this.redeemPoints = this.subtotal;
                            }
                        },

                        async placeOrder() {
                            this.errors = {};
                            this.submitError = null;

                            if (!this.form.shipping_address.trim()) {
                                this.errors.shipping_address = 'Alamat pengiriman wajib diisi.';
                                return;
                            }

                            this.isSubmitting = true;
                            try {
                                const res = await apiClient.post('/checkout', {
                                    buyer_name: this.form.buyer_name,
                                    buyer_email: this.form.buyer_email,
                                    buyer_phone: this.form.buyer_phone,
                                    shipping_address: this.form.shipping_address,
                                    shipping_note: this.form.shipping_note,
                                    redeem_points: Math.max(0, parseInt(this.redeemPoints) || 0),
                                });
                                const order = res.data.data || {};
                                Alpine.store('cart').clear();
                                window.location.href = '/orders/' + (order.order_number || order.orderNumber || '');
                            } catch (err) {
                                const resp = err.response?.data || {};
                                if (resp.errors) {
                                    for (const key in resp.errors) {
                                        this.errors[key] = Array.isArray(resp.errors[key]) ? resp.errors[key][0] : resp.errors[key];
                                    }
                                }
                                this.submitError = resp.message || 'Gagal membuat pesanan. Silakan coba lagi.';
                            } finally {
                                this.isSubmitting = false;
                            }
                        },

                        formatPrice(price) {
                            if (!price && price !== 0) return '';
                            return 'Rp ' + Number(price).toLocaleString('id-ID');
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
@endsection

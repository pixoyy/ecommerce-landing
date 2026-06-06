@extends('layouts.customer')

@section('title', 'Detail Pesanan')
@section('orders-active', 'bg-stone-100 text-stone-900')

@section('customer-content')
    <div x-data="orderDetail()" x-init="init()">
        {{-- Loading --}}
        <div x-show="isLoading" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        {{-- Error --}}
        <div x-show="error && !isLoading" class="text-center py-16">
            <p class="text-stone-500" x-text="error"></p>
            <button @click="init()" class="mt-4 text-sm text-stone-900 underline">Coba lagi</button>
        </div>

        <template x-if="!isLoading && !error && order">
            <div>
                {{-- Back link --}}
                <a href="/orders" class="inline-flex items-center gap-1 text-sm text-stone-500 hover:text-stone-900 transition-colors mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Pesanan
                </a>

                {{-- Header --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h1 class="text-xl font-bold text-stone-900" x-text="order.order_number"></h1>
                            <p class="text-sm text-stone-500 mt-0.5">Dibuat: <span x-text="formatDate(order.created_at)"></span></p>
                            <p x-show="order.paid_at" class="text-sm text-stone-500 mt-0.5">Dibayar: <span x-text="formatDate(order.paid_at)"></span></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium shrink-0"
                              :class="statusClass(order.status)"
                              x-text="statusLabel(order.status)"></span>
                    </div>
                </div>

                {{-- Items --}}
                <div class="mt-4 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Item Pesanan</h2>
                    <div class="space-y-3">
                        <template x-for="item in (order.items || [])" :key="item.id">
                            <div class="flex gap-3">
                                <div class="w-16 h-16 shrink-0 bg-stone-100 rounded-lg overflow-hidden">
                                    <img :src="item.product_image || item.product?.thumbnail" :alt="item.product_name" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-stone-900" x-text="item.product_name"></p>
                                    <p class="text-sm text-stone-500" x-text="item.variant_label"></p>
                                    <p class="text-sm text-stone-500" x-text="item.quantity + ' x ' + formatPrice(item.price)"></p>
                                </div>
                                <span class="text-sm font-semibold text-stone-900 shrink-0" x-text="formatPrice(item.subtotal)"></span>
                            </div>
                        </template>
                    </div>
                    <div class="mt-4 pt-4 border-t border-stone-200 flex justify-between font-semibold text-stone-900">
                        <span>Total</span>
                        <span x-text="formatPrice(order.total)"></span>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="mt-4 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Informasi Pengiriman</h2>
                    <div class="text-sm space-y-1.5">
                        <p><span class="text-stone-500">Penerima:</span> <span class="text-stone-900 font-medium" x-text="order.buyer_name"></span></p>
                        <p><span class="text-stone-500">Email:</span> <span class="text-stone-900" x-text="order.buyer_email"></span></p>
                        <p><span class="text-stone-500">Telepon:</span> <span class="text-stone-900" x-text="order.buyer_phone"></span></p>
                        <p><span class="text-stone-500">Alamat:</span> <span class="text-stone-900" x-text="order.shipping_address"></span></p>
                        <p x-show="order.shipping_note">
                            <span class="text-stone-500">Catatan:</span> <span class="text-stone-900" x-text="order.shipping_note"></span>
                        </p>
                    </div>
                </div>

                {{-- Payment Section --}}
                <div class="mt-4 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Pembayaran</h2>

                    {{-- No payment --}}
                    <template x-if="!order.payments || order.payments.length === 0">
                        <div>
                            <p class="text-sm text-yellow-600 font-medium">Belum Dibayar</p>
                            <a :href="'/orders/' + order.order_number + '/payment'"
                               class="mt-3 inline-block bg-stone-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors">
                                Upload Bukti Transfer
                            </a>
                        </div>
                    </template>

                    {{-- Has payments --}}
                    <template x-for="payment in (order.payments || [])" :key="payment.id">
                        <div>
                            {{-- Pending --}}
                            <template x-if="payment.status === 'pending'">
                                <div>
                                    <p class="text-sm text-yellow-600 font-medium">Menunggu Konfirmasi</p>
                                    <div class="mt-3 flex items-start gap-4">
                                        <img :src="payment.proof_url" alt="Bukti Transfer"
                                             class="w-24 h-24 object-cover rounded-lg border border-stone-200">
                                        <div class="text-sm text-stone-600">
                                            <p>Bank: <span class="font-medium text-stone-900" x-text="payment.bank_name || '-'"></span></p>
                                            <p>No. Rekening: <span class="font-medium text-stone-900" x-text="payment.account_number || '-'"></span></p>
                                            <p>A/n: <span class="font-medium text-stone-900" x-text="payment.account_name || '-'"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Approved --}}
                            <template x-if="payment.status === 'approved'">
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Lunas</span>
                                    <div class="mt-3 text-sm text-stone-600">
                                        <p>Dibayar: <span class="font-medium text-stone-900" x-text="formatPrice(payment.amount)"></span></p>
                                        <p x-show="order.paid_at">Tanggal: <span class="font-medium text-stone-900" x-text="formatDate(order.paid_at)"></span></p>
                                    </div>
                                </div>
                            </template>

                            {{-- Rejected --}}
                            <template x-if="payment.status === 'rejected'">
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                    <p class="mt-2 text-sm text-red-600" x-show="payment.rejected_reason" x-text="payment.rejected_reason"></p>
                                    <a :href="'/orders/' + order.order_number + '/payment'"
                                       class="mt-3 inline-block bg-stone-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors">
                                        Upload Ulang
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Shipment Tracking --}}
                <div class="mt-4 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Pengiriman</h2>

                    {{-- No shipment --}}
                    <div x-show="!order.shipment">
                        <p class="text-sm text-stone-500">Belum ada data pengiriman.</p>
                    </div>

                    {{-- Shipment exists --}}
                    <template x-if="order.shipment">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm text-stone-500">Kurir</p>
                                    <p class="font-semibold text-stone-900" x-text="order.shipment.courier"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-stone-500">No. Resi</p>
                                    <p class="font-mono font-semibold text-stone-900" x-text="order.shipment.tracking_number"></p>
                                </div>
                            </div>

                            {{-- Timeline --}}
                            <div x-show="order.shipment.logs && order.shipment.logs.length > 0" class="relative mt-4 pt-4 border-t border-stone-100">
                                <template x-for="(log, idx) in order.shipment.logs" :key="log.id">
                                    <div class="flex gap-4 pb-6 relative last:pb-0">
                                        <div class="flex flex-col items-center">
                                            <div class="w-3.5 h-3.5 rounded-full shrink-0 z-10"
                                                 :class="idx === order.shipment.logs.length - 1
                                                     ? 'bg-stone-900 ring-4 ring-stone-100'
                                                     : 'bg-emerald-500 ring-4 ring-emerald-100'">
                                                <svg x-show="idx < order.shipment.logs.length - 1" class="w-3.5 h-3.5 text-white p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div x-show="idx < order.shipment.logs.length - 1"
                                                 class="w-0.5 flex-1 bg-stone-200 -mt-1"></div>
                                        </div>
                                        <div class="flex-1 min-w-0 -mt-0.5">
                                            <p class="font-medium text-stone-900 text-sm" x-text="log.status"></p>
                                            <p x-show="log.note" class="text-xs text-stone-500 mt-0.5" x-text="log.note"></p>
                                            <p class="text-xs text-stone-400 mt-0.5">
                                                <span x-show="log.location" x-text="log.location"></span>
                                                <span x-show="log.location && log.created_at"> — </span>
                                                <span x-text="formatDate(log.created_at)"></span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <p x-show="!order.shipment.logs || order.shipment.logs.length === 0" class="text-sm text-stone-500 mt-2">
                                Belum ada riwayat pengiriman.
                            </p>

                            <a :href="'/orders/' + order.order_number + '/tracking'" class="mt-3 inline-block text-xs text-stone-500 hover:text-stone-900 underline">
                                Lihat detail pengiriman
                            </a>
                        </div>
                    </template>
                </div>

                {{-- Cancel button --}}
                <div x-show="order.status === 1 || order.status === 2" class="mt-4">
                    <button @click="cancelModalOpen = true"
                            class="px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                        Batalkan Pesanan
                    </button>
                </div>

                {{-- Review Section (only for delivered orders) --}}
                <div x-show="order.status === 4" class="mt-4 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Ulasan Produk</h2>
                    <template x-for="item in (order.items || [])" :key="item.id">
                        <div class="py-4 border-b border-stone-100 last:border-0">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 shrink-0 bg-stone-100 rounded-lg overflow-hidden">
                                    <img :src="item.product_image || item.product?.thumbnail" :alt="item.product_name" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-stone-900 text-sm" x-text="item.product_name"></p>
                                    <p class="text-xs text-stone-500" x-text="item.variant_label"></p>
                                </div>
                            </div>

                            {{-- Already reviewed --}}
                            <div x-show="reviewedItems[item.id]" class="text-sm text-emerald-600 font-medium">
                                Ulasan sudah diberikan
                            </div>

                            {{-- Review form --}}
                            <div x-show="!reviewedItems[item.id]">
                                {{-- Star selector --}}
                                <div class="flex items-center gap-1 mb-3">
                                    <template x-for="star in 5" :key="star">
                                        <button @click="setRating(item.id, star)" @mouseenter="hoverRating = star" @mouseleave="hoverRating = 0"
                                                class="p-0.5 transition-colors">
                                            <svg class="w-6 h-6" :class="star <= (hoverRating || reviewForms[item.id]?.rating || 0) ? 'text-amber-400' : 'text-stone-300'" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>

                                {{-- Textarea --}}
                                <textarea x-model="reviewForms[item.id].review" maxlength="1000" rows="2"
                                          placeholder="Tulis ulasan Anda (opsional)..."
                                          class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"></textarea>
                                <div class="flex justify-between items-center mt-1">
                                    <p x-show="reviewErrors[item.id]" class="text-xs text-red-600" x-text="reviewErrors[item.id]"></p>
                                    <p class="text-xs text-stone-400 ml-auto" x-text="(reviewForms[item.id]?.review?.length || 0) + '/1000'"></p>
                                </div>

                                {{-- Submit --}}
                                <div class="mt-3 flex items-center gap-3">
                                    <button @click="submitReview(item)" :disabled="isSubmittingReview[item.id]"
                                            class="bg-stone-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50">
                                        <span x-show="!isSubmittingReview[item.id]">Kirim Ulasan</span>
                                        <span x-show="isSubmittingReview[item.id]">Mengirim...</span>
                                    </button>
                                    <span x-show="reviewSuccess[item.id]" class="text-sm text-emerald-600 font-medium">Ulasan berhasil dikirim!</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Cancel confirmation modal --}}
        <div x-show="cancelModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-black/40" @click="cancelModalOpen = false"></div>
            <div class="relative bg-white rounded-xl p-6 max-w-sm w-full shadow-xl">
                <h3 class="text-lg font-semibold text-stone-900">Batalkan Pesanan?</h3>
                <p class="mt-1 text-sm text-stone-500">Yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="mt-4 flex gap-3 justify-end">
                    <button @click="cancelModalOpen = false" class="px-4 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg transition-colors">Tutup</button>
                    <button @click="confirmCancel()" :disabled="isCancelling"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                            x-text="isCancelling ? 'Membatalkan...' : 'Ya, Batalkan'"></button>
                </div>
            </div>
        </div>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('orderDetail', () => ({
                        order: null,
                        isLoading: false,
                        error: null,
                        cancelModalOpen: false,
                        isCancelling: false,
                        reviewedItems: {},
                        reviewForms: {},
                        reviewErrors: {},
                        reviewSuccess: {},
                        isSubmittingReview: {},
                        hoverRating: 0,

                        STATUSES: {
                            1: { label: 'Menunggu Pembayaran', class: 'bg-yellow-100 text-yellow-800' },
                            2: { label: 'Diproses', class: 'bg-blue-100 text-blue-800' },
                            3: { label: 'Dikirim', class: 'bg-indigo-100 text-indigo-800' },
                            4: { label: 'Selesai', class: 'bg-emerald-100 text-emerald-800' },
                            5: { label: 'Dibatalkan', class: 'bg-red-100 text-red-800' },
                        },

                        async init() {
                            if (!Alpine.store('auth').isAuthenticated) {
                                window.location.href = '/login';
                                return;
                            }
                            const pathParts = window.location.pathname.split('/');
                            const ordersIdx = pathParts.indexOf('orders');
                            const orderNumber = ordersIdx !== -1 ? pathParts[ordersIdx + 1] : null;
                            if (!orderNumber) {
                                this.error = 'Pesanan tidak ditemukan.';
                                return;
                            }
                            await this.loadOrder(orderNumber);
                        },

                        statusLabel(status) {
                            return this.STATUSES[status]?.label || 'Unknown';
                        },

                        statusClass(status) {
                            return this.STATUSES[status]?.class || 'bg-stone-100 text-stone-800';
                        },

                        async loadOrder(orderNumber) {
                            this.isLoading = true;
                            this.error = null;
                            try {
                                const res = await apiClient.get('/orders/' + orderNumber);
                                this.order = res.data.data || {};
                                (this.order.items || []).forEach(item => {
                                    if (!this.reviewForms[item.id]) {
                                        this.reviewForms[item.id] = { rating: 0, review: '' };
                                    }
                                });
                            } catch (err) {
                                if (err.response?.status === 404) {
                                    this.error = 'Pesanan tidak ditemukan.';
                                } else {
                                    this.error = 'Gagal memuat detail pesanan.';
                                }
                            } finally {
                                this.isLoading = false;
                            }
                        },

                        async confirmCancel() {
                            this.isCancelling = true;
                            try {
                                await apiClient.post('/orders/' + this.order.id + '/cancel');
                                this.cancelModalOpen = false;
                                await this.loadOrder(this.order.order_number);
                            } catch (err) {
                                const msg = err.response?.data?.message || 'Gagal membatalkan pesanan.';
                                alert(msg);
                            } finally {
                                this.isCancelling = false;
                            }
                        },

                        setRating(itemId, rating) {
                            if (!this.reviewForms[itemId]) {
                                this.reviewForms[itemId] = { rating: 0, review: '' };
                            }
                            this.reviewForms[itemId].rating = rating;
                        },

                        async submitReview(item) {
                            this.reviewErrors[item.id] = null;
                            this.reviewSuccess[item.id] = false;

                            const form = this.reviewForms[item.id];
                            if (!form || !form.rating) {
                                this.reviewErrors[item.id] = 'Harap pilih rating.';
                                return;
                            }

                            this.isSubmittingReview[item.id] = true;
                            try {
                                await apiClient.post('/orders/' + this.order.id + '/reviews', {
                                    product_variant_id: item.product_variant_id,
                                    rating: form.rating,
                                    review: form.review || '',
                                });
                                this.reviewSuccess[item.id] = true;
                                this.reviewedItems[item.id] = true;
                            } catch (err) {
                                const msg = err.response?.data?.message || 'Gagal mengirim ulasan.';
                                this.reviewErrors[item.id] = msg;
                            } finally {
                                this.isSubmittingReview[item.id] = false;
                            }
                        },

                        formatPrice(price) {
                            if (!price && price !== 0) return '';
                            return 'Rp ' + Number(price).toLocaleString('id-ID');
                        },

                        formatDate(date) {
                            if (!date) return '';
                            const d = new Date(date);
                            return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
@endsection

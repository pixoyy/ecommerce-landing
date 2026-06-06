@extends('layouts.customer')

@section('title', 'Pesanan Saya')
@section('orders-active', 'bg-stone-100 text-stone-900')

@section('customer-content')
    <div x-data="orderList()" x-init="init()">
        <h1 class="text-2xl font-bold text-stone-900">Pesanan Saya</h1>

        {{-- Status tabs --}}
        <div class="mt-6 flex gap-1 overflow-x-auto pb-1" x-data="{
            tabs: [
                { key: '', label: 'Semua' },
                { key: '1', label: 'Menunggu Pembayaran' },
                { key: '2', label: 'Diproses' },
                { key: '3', label: 'Dikirim' },
                { key: '4', label: 'Selesai' },
                { key: '5', label: 'Dibatalkan' },
            ]
        }">
            <template x-for="tab in tabs" :key="tab.key">
                <button @click="switchTab(tab.key)"
                        class="whitespace-nowrap px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        :class="activeTab === tab.key
                            ? 'bg-stone-900 text-white'
                            : 'text-stone-600 hover:bg-stone-100'">
                    <span x-text="tab.label"></span>
                </button>
            </template>
        </div>

        {{-- Loading --}}
        <div x-show="isLoading && orders.length === 0" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        {{-- Error --}}
        <div x-show="error" class="text-center py-16">
            <p class="text-stone-500" x-text="error"></p>
            <button @click="loadOrders()" class="mt-4 text-sm text-stone-900 underline">Coba lagi</button>
        </div>

        {{-- Empty --}}
        <div x-show="!isLoading && !error && orders.length === 0" class="text-center py-16">
            <svg class="mx-auto w-16 h-16 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="mt-4 text-stone-500">Belum ada pesanan.</p>
            <a href="/products" class="mt-4 inline-block text-sm text-stone-900 underline">Mulai Belanja</a>
        </div>

        {{-- Order list --}}
        <div x-show="!isLoading && !error && orders.length > 0" class="mt-4 space-y-3">
            <template x-for="order in orders" :key="order.id">
                <a :href="'/orders/' + order.order_number"
                   class="block bg-white border border-stone-200 rounded-xl p-4 hover:border-stone-400 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-stone-900" x-text="order.order_number"></p>
                            <p class="text-sm text-stone-500 mt-0.5" x-text="formatDate(order.created_at)"></p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="statusClass(order.status)"
                              x-text="statusLabel(order.status)"></span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <span class="text-stone-500" x-text="(order.total_items || order.items_count || 0) + ' barang'"></span>
                        <span class="font-semibold text-stone-900" x-text="formatPrice(order.total)"></span>
                    </div>
                </a>
            </template>
        </div>

        {{-- Pagination --}}
        <div x-show="meta.last_page > 1" class="mt-6 flex justify-center items-center gap-2">
            <button @click="loadOrders(meta.current_page - 1)" :disabled="meta.current_page === 1"
                    class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50 transition-colors">
                Sebelumnya
            </button>
            <span class="text-sm text-stone-500">
                Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
            </span>
            <button @click="loadOrders(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page"
                    class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50 transition-colors">
                Berikutnya
            </button>
        </div>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('orderList', () => ({
                        orders: [],
                        meta: { current_page: 1, last_page: 1, total: 0 },
                        isLoading: false,
                        error: null,
                        activeTab: '',

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
                            await this.loadOrders();
                        },

                        statusLabel(status) {
                            return this.STATUSES[status]?.label || 'Unknown';
                        },

                        statusClass(status) {
                            return this.STATUSES[status]?.class || 'bg-stone-100 text-stone-800';
                        },

                        async switchTab(key) {
                            this.activeTab = key;
                            this.meta.current_page = 1;
                            await this.loadOrders();
                        },

                        async loadOrders(page) {
                            if (page) this.meta.current_page = page;
                            this.isLoading = true;
                            this.error = null;
                            try {
                                const params = { page: this.meta.current_page, per_page: 10 };
                                if (this.activeTab) params.status = this.activeTab;
                                const res = await apiClient.get('/orders', { params });
                                this.orders = res.data.data || [];
                                this.meta = res.data.meta || { current_page: 1, last_page: 1, total: 0 };
                            } catch (err) {
                                if (err.response?.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                this.error = 'Gagal memuat pesanan.';
                            } finally {
                                this.isLoading = false;
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

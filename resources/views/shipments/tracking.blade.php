@extends('layouts.customer')

@section('title', 'Lacak Pengiriman')
@section('orders-active', 'bg-stone-100 text-stone-900')

@section('customer-content')
    <div x-data="shipmentTracking()" x-init="init()">
        <div class="flex items-center gap-2 mb-2">
            <a href="/orders" class="text-sm text-stone-500 hover:text-stone-900 transition-colors">Pesanan Saya</a>
            <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-stone-900">Lacak Pengiriman</h1>

        {{-- Loading --}}
        <div x-show="isLoading" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        {{-- Error --}}
        <div x-show="error && !isLoading" class="text-center py-16">
            <p class="text-stone-500" x-text="error"></p>
            <button @click="init()" class="mt-4 text-sm text-stone-900 underline">Coba lagi</button>
        </div>

        {{-- Tracking content --}}
        <template x-if="!isLoading && !error">
            <div>
                {{-- No shipment --}}
                <div x-show="!shipment" class="mt-6 bg-white border border-stone-200 rounded-xl p-6 text-center">
                    <svg class="mx-auto w-16 h-16 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-4 text-stone-500">Belum ada data pengiriman.</p>
                </div>

                {{-- Shipment exists --}}
                <div x-show="shipment" class="mt-6">
                    {{-- Courier info --}}
                    <div class="bg-white border border-stone-200 rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-stone-500">Kurir</p>
                                <p class="font-semibold text-stone-900 text-lg" x-text="shipment.courier"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-stone-500">No. Resi</p>
                                <p class="font-mono font-semibold text-stone-900" x-text="shipment.tracking_number"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="mt-4 bg-white border border-stone-200 rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-stone-900 mb-6">Riwayat Pengiriman</h2>

                        {{-- No logs --}}
                        <p x-show="!shipment.logs || shipment.logs.length === 0" class="text-sm text-stone-500 text-center py-4">
                            Belum ada riwayat pengiriman.
                        </p>

                        {{-- Timeline stepper --}}
                        <div x-show="shipment.logs && shipment.logs.length > 0" class="relative">
                            <template x-for="(log, idx) in shipment.logs" :key="log.id">
                                <div class="flex gap-4 pb-8 relative last:pb-0">
                                    {{-- Vertical line --}}
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 rounded-full shrink-0 z-10"
                                             :class="idx === shipment.logs.length - 1
                                                 ? 'bg-stone-900 ring-4 ring-stone-100'
                                                 : 'bg-emerald-500 ring-4 ring-emerald-100'">
                                            <svg x-show="idx < shipment.logs.length - 1" class="w-4 h-4 text-white p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div x-show="idx < shipment.logs.length - 1"
                                             class="w-0.5 flex-1 bg-stone-200 -mt-1"></div>
                                    </div>
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0 -mt-0.5">
                                        <p class="font-medium text-stone-900" x-text="log.status"></p>
                                        <p x-show="log.note" class="text-sm text-stone-500 mt-0.5" x-text="log.note"></p>
                                        <p class="text-sm text-stone-400 mt-0.5">
                                            <span x-show="log.location" x-text="log.location"></span>
                                            <span x-show="log.location && log.created_at"> — </span>
                                            <span x-text="formatDate(log.created_at)"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('shipmentTracking', () => ({
                        shipment: null,
                        isLoading: false,
                        error: null,

                        async init() {
                            if (!Alpine.store('auth').isAuthenticated) {
                                window.location.href = '/login';
                                return;
                            }
                            const pathParts = window.location.pathname.split('/');
                            const idx = pathParts.indexOf('tracking');
                            const orderNumber = idx !== -1 ? pathParts[idx - 1] : null;
                            if (!orderNumber) {
                                this.error = 'Pesanan tidak ditemukan.';
                                return;
                            }
                            await this.loadTracking(orderNumber);
                        },

                        async loadTracking(orderNumber) {
                            this.isLoading = true;
                            this.error = null;
                            try {
                                const res = await apiClient.get('/orders/' + orderNumber + '/tracking');
                                this.shipment = res.data.data || null;
                            } catch (err) {
                                if (err.response?.status === 404) {
                                    this.shipment = null;
                                } else {
                                    this.error = 'Gagal memuat data pengiriman.';
                                }
                            } finally {
                                this.isLoading = false;
                            }
                        },

                        formatDate(date) {
                            if (!date) return '';
                            const d = new Date(date);
                            return d.toLocaleDateString('id-ID', {
                                year: 'numeric', month: 'long', day: 'numeric',
                                hour: '2-digit', minute: '2-digit',
                            });
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
@endsection

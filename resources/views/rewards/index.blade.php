@extends('layouts.customer')

@section('title', 'Poin Reward')
@section('rewards-active', 'bg-stone-100 text-stone-900')

@section('customer-content')
    <div x-data="rewardsPage()" x-init="init()">
        <h1 class="text-2xl font-bold text-stone-900">Poin Reward</h1>

        {{-- Loading --}}
        <div x-show="isLoading" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        <template x-if="!isLoading">
            <div>
                {{-- Balance card --}}
                <div class="mt-6 bg-gradient-to-br from-stone-900 to-stone-700 rounded-xl p-6 text-white">
                    <div class="flex items-center gap-3">
                        <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-stone-300">Poin Reward Anda</p>
                            <p class="text-3xl font-bold" x-text="Number(balance).toLocaleString('id-ID')"></p>
                        </div>
                    </div>
                </div>

                {{-- Transaction history --}}
                <div class="mt-6 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Riwayat Transaksi</h2>

                    {{-- Empty --}}
                    <p x-show="transactions.length === 0" class="text-sm text-stone-500 text-center py-8">
                        Belum ada transaksi poin.
                    </p>

                    {{-- Transaction list --}}
                    <div x-show="transactions.length > 0" class="space-y-3">
                        <template x-for="tx in transactions" :key="tx.id">
                            <div class="flex items-center justify-between py-3 border-b border-stone-100 last:border-0">
                                <div class="flex items-start gap-3 min-w-0">
                                    {{-- Type icon --}}
                                    <div class="mt-0.5 shrink-0">
                                        <template x-if="tx.type === 'credit'">
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </template>
                                        <template x-if="tx.type === 'debit'">
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-stone-900 truncate" x-text="tx.description"></p>
                                        <p class="text-xs text-stone-400" x-text="formatDate(tx.created_at)"></p>
                                        <a x-show="tx.order_number" :href="'/orders/' + tx.order_number"
                                           class="text-xs text-stone-500 hover:text-stone-900 underline mt-0.5 inline-block"
                                           x-text="tx.order_number"></a>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-4">
                                    <span class="text-sm font-semibold"
                                          :class="tx.type === 'credit' ? 'text-emerald-600' : 'text-red-600'"
                                          x-text="(tx.type === 'credit' ? '+' : '-') + Number(tx.amount).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Pagination --}}
                    <div x-show="meta.last_page > 1" class="mt-6 flex justify-center items-center gap-2">
                        <button @click="loadTransactions(meta.current_page - 1)" :disabled="meta.current_page === 1"
                                class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50 transition-colors">
                            Sebelumnya
                        </button>
                        <span class="text-sm text-stone-500">
                            Halaman <span x-text="meta.current_page"></span> dari <span x-text="meta.last_page"></span>
                        </span>
                        <button @click="loadTransactions(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page"
                                class="px-3 py-2 text-sm border border-stone-300 rounded-lg hover:bg-stone-50 disabled:opacity-50 transition-colors">
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
                    Alpine.data('rewardsPage', () => ({
                        balance: 0,
                        transactions: [],
                        meta: { current_page: 1, last_page: 1, total: 0 },
                        isLoading: false,

                        async init() {
                            if (!Alpine.store('auth').isAuthenticated) {
                                window.location.href = '/login';
                                return;
                            }
                            await Promise.all([this.loadBalance(), this.loadTransactions()]);
                        },

                        async loadBalance() {
                            try {
                                const res = await apiClient.get('/rewards/balance');
                                this.balance = res.data.data?.balance || 0;
                            } catch {
                                this.balance = 0;
                            }
                        },

                        async loadTransactions(page) {
                            if (page) this.meta.current_page = page;
                            this.isLoading = true;
                            try {
                                const res = await apiClient.get('/rewards/transactions', {
                                    params: { page: this.meta.current_page, per_page: 10 }
                                });
                                this.transactions = res.data.data || [];
                                this.meta = res.data.meta || { current_page: 1, last_page: 1, total: 0 };
                            } catch {
                                this.transactions = [];
                            } finally {
                                this.isLoading = false;
                            }
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

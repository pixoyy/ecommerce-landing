@extends('layouts.customer')

@section('title', 'Pembayaran')
@section('orders-active', 'bg-stone-100 text-stone-900')

@section('customer-content')
    <div x-data="paymentForm()" x-init="init()">
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
                <h1 class="text-2xl font-bold text-stone-900">Upload Bukti Pembayaran</h1>

                {{-- Order Info --}}
                <div class="mt-6 bg-white border border-stone-200 rounded-xl p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-stone-500">No. Pesanan</span>
                            <p class="font-medium text-stone-900 mt-0.5" x-text="order.order_number"></p>
                        </div>
                        <div>
                            <span class="text-stone-500">Status</span>
                            <p class="mt-0.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span>
                            </p>
                        </div>
                        <div>
                            <span class="text-stone-500">Total Pembayaran</span>
                            <p class="font-semibold text-stone-900 mt-0.5 text-lg" x-text="formatPrice(order.total)"></p>
                        </div>
                    </div>
                </div>

                {{-- Bank Accounts --}}
                <div class="mt-6 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Transfer ke Rekening Berikut</h2>
                    <p class="text-sm text-stone-500 mb-4">
                        Silakan transfer <strong class="text-stone-900" x-text="formatPrice(order.total)"></strong> ke salah satu rekening di bawah ini.
                    </p>
                    <div class="space-y-3">
                        <template x-for="account in paymentAccounts" :key="account.id">
                            <div class="flex items-center justify-between p-4 border border-stone-200 rounded-xl">
                                <div>
                                    <p class="font-semibold text-stone-900" x-text="account.bank_name"></p>
                                    <p class="text-sm text-stone-500" x-text="account.account_name"></p>
                                    <p class="text-base font-mono font-semibold text-stone-900 mt-0.5" x-text="account.account_number"></p>
                                </div>
                                <button @click="copyNumber(account.account_number)"
                                        class="shrink-0 px-3 py-1.5 text-xs font-medium text-stone-600 border border-stone-300 rounded-lg hover:bg-stone-50 transition-colors"
                                        x-text="copiedId === account.id ? 'Tersalin!' : 'Salin No. Rekening'">
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Upload Form --}}
                <div class="mt-6 bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Upload Bukti Transfer</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Jumlah Transfer</label>
                            <input type="number" x-model="form.amount"
                                   class="w-full sm:w-64 px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                   :class="errors.amount ? 'border-red-500' : ''">
                            <p x-show="errors.amount" class="mt-1 text-xs text-red-600" x-text="errors.amount"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">File Bukti Transfer <span class="text-red-500">*</span></label>
                            <input type="file" accept="image/jpeg,image/jpg,image/png" @change="handleFileSelect($event)"
                                   class="block w-full text-sm text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200 transition-colors"
                                   :class="errors.file ? 'border-red-500' : ''">
                            <p x-show="errors.file" class="mt-1 text-xs text-red-600" x-text="errors.file"></p>
                            <p class="mt-1 text-xs text-stone-400">Format: JPG/PNG, maksimal 2MB</p>

                            {{-- Preview --}}
                            <div x-show="previewUrl" class="mt-3">
                                <p class="text-xs text-stone-500 mb-1">Pratinjau:</p>
                                <img :src="previewUrl" alt="Pratinjau bukti transfer"
                                     class="max-w-xs rounded-lg border border-stone-200">
                            </div>
                        </div>

                        <button @click="submitPayment()" :disabled="isSubmitting"
                                class="bg-stone-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">Kirim Bukti Pembayaran</span>
                            <span x-show="isSubmitting">Mengirim...</span>
                        </button>

                        {{-- Submit error --}}
                        <div x-show="submitError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-lg text-sm" x-text="submitError"></div>

                        {{-- Success --}}
                        <div x-show="success" class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2.5 rounded-lg text-sm">
                            <p class="font-medium">Pembayaran berhasil diupload, menunggu konfirmasi admin.</p>
                            <a :href="'/orders/' + order.order_number" class="mt-2 inline-block text-sm underline">Lihat detail pesanan</a>
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
                    Alpine.data('paymentForm', () => ({
                        order: null,
                        paymentAccounts: [],
                        isLoading: false,
                        error: null,
                        isSubmitting: false,
                        submitError: null,
                        success: false,
                        copiedId: null,
                        previewUrl: null,
                        selectedFile: null,
                        form: {
                            amount: 0,
                        },
                        errors: {},

                        async init() {
                            if (!Alpine.store('auth').isAuthenticated) {
                                window.location.href = '/login';
                                return;
                            }
                            const pathParts = window.location.pathname.split('/');
                            const orderNumber = pathParts[pathParts.indexOf('orders') + 1];
                            if (!orderNumber) {
                                this.error = 'Pesanan tidak ditemukan.';
                                return;
                            }
                            await Promise.all([this.loadOrder(orderNumber), this.loadPaymentAccounts()]);
                        },

                        async loadOrder(orderNumber) {
                            this.isLoading = true;
                            this.error = null;
                            try {
                                const res = await apiClient.get('/orders/' + orderNumber);
                                this.order = res.data.data || {};
                                this.form.amount = this.order.total || 0;
                            } catch (err) {
                                if (err.response?.status === 404) {
                                    this.error = 'Pesanan tidak ditemukan.';
                                } else {
                                    this.error = 'Gagal memuat data pesanan.';
                                }
                            } finally {
                                this.isLoading = false;
                            }
                        },

                        async loadPaymentAccounts() {
                            try {
                                const res = await apiClient.get('/payment-accounts');
                                this.paymentAccounts = res.data.data || [];
                            } catch {
                                this.paymentAccounts = [];
                            }
                        },

                        handleFileSelect(event) {
                            this.errors.file = null;
                            const file = event.target.files[0];
                            if (!file) {
                                this.selectedFile = null;
                                this.previewUrl = null;
                                return;
                            }
                            const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
                            if (!allowed.includes(file.type)) {
                                this.errors.file = 'Hanya file gambar JPG/PNG yang diizinkan.';
                                this.selectedFile = null;
                                this.previewUrl = null;
                                event.target.value = '';
                                return;
                            }
                            if (file.size > 2 * 1024 * 1024) {
                                this.errors.file = 'Ukuran file maksimal 2MB.';
                                this.selectedFile = null;
                                this.previewUrl = null;
                                event.target.value = '';
                                return;
                            }
                            this.selectedFile = file;
                            const reader = new FileReader();
                            reader.onload = (e) => { this.previewUrl = e.target.result; };
                            reader.readAsDataURL(file);
                        },

                        async submitPayment() {
                            this.errors = {};
                            this.submitError = null;
                            this.success = false;

                            if (!this.selectedFile) {
                                this.errors.file = 'Harap pilih file bukti transfer.';
                                return;
                            }
                            if (!this.form.amount || this.form.amount <= 0) {
                                this.errors.amount = 'Jumlah transfer wajib diisi.';
                                return;
                            }
                            if (parseInt(this.form.amount) !== parseInt(this.order.total)) {
                                this.errors.amount = 'Jumlah pembayaran harus sama dengan total pesanan.';
                                return;
                            }

                            this.isSubmitting = true;
                            try {
                                const fd = new FormData();
                                fd.append('proof', this.selectedFile);
                                fd.append('amount', this.form.amount);
                                const res = await apiClient.post('/orders/' + this.order.id + '/payment', fd, {
                                    headers: { 'Content-Type': 'multipart/form-data' },
                                });
                                this.success = true;
                            } catch (err) {
                                const resp = err.response?.data || {};
                                if (resp.errors) {
                                    for (const key in resp.errors) {
                                        this.errors[key] = Array.isArray(resp.errors[key]) ? resp.errors[key][0] : resp.errors[key];
                                    }
                                }
                                this.submitError = resp.message || 'Gagal mengupload pembayaran. Silakan coba lagi.';
                            } finally {
                                this.isSubmitting = false;
                            }
                        },

                        async copyNumber(number) {
                            try {
                                await navigator.clipboard.writeText(number);
                                const account = this.paymentAccounts.find(a => a.account_number === number);
                                if (account) this.copiedId = account.id;
                                setTimeout(() => { this.copiedId = null; }, 2000);
                            } catch {
                                alert('Gagal menyalin nomor rekening.');
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

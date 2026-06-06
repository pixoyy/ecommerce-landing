@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <div class="w-full max-w-sm" x-data="registerForm()">
        <h1 class="text-2xl font-bold text-stone-900 text-center">Daftar</h1>
        <p class="text-sm text-stone-500 text-center mt-1">Buat akun EssenseLuxe Anda</p>

        <template x-if="errorMessage">
            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm" x-text="errorMessage"></div>
        </template>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-stone-700">Nama</label>
                <input type="text" id="name" x-model="form.name"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                       :class="errors.name ? 'border-red-300' : ''">
                <template x-if="errors.name">
                    <p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p>
                </template>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-stone-700">Email</label>
                <input type="email" id="email" x-model="form.email"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                       :class="errors.email ? 'border-red-300' : ''">
                <template x-if="errors.email">
                    <p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p>
                </template>
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-stone-700">Telepon <span class="text-stone-400">(opsional)</span></label>
                <input type="text" id="phone" x-model="form.phone"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-stone-700">Password</label>
                <input type="password" id="password" x-model="form.password"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                       :class="errors.password ? 'border-red-300' : ''">
                <template x-if="errors.password">
                    <p class="mt-1 text-sm text-red-600" x-text="errors.password[0]"></p>
                </template>
                <p class="mt-1 text-xs text-stone-400" x-show="form.password.length > 0 && form.password.length < 8">Minimal 8 karakter</p>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-stone-700">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" x-model="form.password_confirmation"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
            </div>
            <button type="submit" :disabled="isLoading"
                    class="w-full bg-stone-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50">
                <span x-show="!isLoading">Daftar</span>
                <span x-show="isLoading">Memproses...</span>
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-stone-500">
            Sudah punya akun? <a href="/login" class="text-stone-900 font-medium hover:underline">Masuk</a>
        </p>
    </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('registerForm', () => ({
                        form: { name: '', email: '', phone: '', password: '', password_confirmation: '' },
                        errors: {},
                        errorMessage: '',
                        isLoading: false,

                        async submit() {
                            this.isLoading = true;
                            this.errors = {};
                            this.errorMessage = '';
                            try {
                                const res = await apiClient.post('/register', this.form);
                                const { user, token } = res.data.data;
                                Alpine.store('auth').setAuth(user, token);
                                window.location.href = '/';
                            } catch (err) {
                                const status = err.response?.status;
                                const data = err.response?.data;
                                if (status === 422) {
                                    this.errors = data.errors || {};
                                    this.errorMessage = data.message || '';
                                } else {
                                    this.errorMessage = data?.message || 'Terjadi kesalahan. Silakan coba lagi.';
                                }
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

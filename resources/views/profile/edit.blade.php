@extends('layouts.customer')

@section('title', 'Profil')
@section('profile-active', 'bg-stone-100 text-stone-900')

@section('customer-content')
    <div x-data="profileEdit()" x-init="init()">
        <h1 class="text-2xl font-bold text-stone-900">Profil</h1>

        {{-- Loading --}}
        <div x-show="isLoading" class="text-center py-16">
            @include('components.loading-spinner', ['size' => 'lg'])
        </div>

        <template x-if="!isLoading">
            <div class="mt-6 space-y-6">
                {{-- Profile form --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Informasi Profil</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Nama</label>
                            <input type="text" x-model="form.name"
                                   class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                   :class="errors.name ? 'border-red-500' : ''">
                            <p x-show="errors.name" class="mt-1 text-xs text-red-600" x-text="errors.name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Email</label>
                            <input type="email" x-model="form.email"
                                   class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                   :class="errors.email ? 'border-red-500' : ''">
                            <p x-show="errors.email" class="mt-1 text-xs text-red-600" x-text="errors.email"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">No. Telepon</label>
                            <input type="tel" x-model="form.phone"
                                   class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                   :class="errors.phone ? 'border-red-500' : ''">
                            <p x-show="errors.phone" class="mt-1 text-xs text-red-600" x-text="errors.phone"></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="saveProfile()" :disabled="isSavingProfile"
                                    class="bg-stone-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50">
                                <span x-show="!isSavingProfile">Simpan</span>
                                <span x-show="isSavingProfile">Menyimpan...</span>
                            </button>
                            <span x-show="profileSuccess" class="text-sm text-emerald-600 font-medium" x-text="profileSuccess"></span>
                        </div>
                    </div>
                </div>

                {{-- Password form --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-4">Ubah Password</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Password Saat Ini</label>
                            <div class="relative">
                                <input :type="showCurrentPw ? 'text' : 'password'" x-model="pwForm.current_password"
                                       class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900 pr-10"
                                       :class="pwErrors.current_password ? 'border-red-500' : ''">
                                <button @click="showCurrentPw = !showCurrentPw" type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="pwErrors.current_password" class="mt-1 text-xs text-red-600" x-text="pwErrors.current_password"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Password Baru</label>
                            <div class="relative">
                                <input :type="showNewPw ? 'text' : 'password'" x-model="pwForm.new_password"
                                       class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900 pr-10"
                                       :class="pwErrors.new_password ? 'border-red-500' : ''">
                                <button @click="showNewPw = !showNewPw" type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-stone-400">Minimal 8 karakter</p>
                            <p x-show="pwErrors.new_password" class="mt-1 text-xs text-red-600" x-text="pwErrors.new_password"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Konfirmasi Password Baru</label>
                            <input :type="showNewPw ? 'text' : 'password'" x-model="pwForm.new_password_confirmation"
                                   class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900"
                                   :class="pwErrors.new_password_confirmation ? 'border-red-500' : ''">
                            <p x-show="pwErrors.new_password_confirmation" class="mt-1 text-xs text-red-600" x-text="pwErrors.new_password_confirmation"></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="changePassword()" :disabled="isSavingPw"
                                    class="bg-stone-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors disabled:opacity-50">
                                <span x-show="!isSavingPw">Simpan Password</span>
                                <span x-show="isSavingPw">Menyimpan...</span>
                            </button>
                            <span x-show="pwSuccess" class="text-sm text-emerald-600 font-medium" x-text="pwSuccess"></span>
                        </div>
                    </div>
                </div>

                {{-- Logout --}}
                <div class="bg-white border border-stone-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-stone-900 mb-2">Sesi</h2>
                    <p class="text-sm text-stone-500 mb-4">Anda dapat keluar dari akun Anda kapan saja.</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('profileEdit', () => ({
                        form: { name: '', email: '', phone: '' },
                        errors: {},
                        profileSuccess: null,
                        isSavingProfile: false,
                        pwForm: { current_password: '', new_password: '', new_password_confirmation: '' },
                        pwErrors: {},
                        pwSuccess: null,
                        isSavingPw: false,
                        showCurrentPw: false,
                        showNewPw: false,
                        isLoading: false,

                        async init() {
                            if (!Alpine.store('auth').isAuthenticated) {
                                window.location.href = '/login';
                                return;
                            }
                            await this.loadProfile();
                        },

                        async loadProfile() {
                            this.isLoading = true;
                            try {
                                const res = await apiClient.get('/profile');
                                const data = res.data.data || {};
                                this.form.name = data.name || '';
                                this.form.email = data.email || '';
                                this.form.phone = data.phone || '';
                            } catch (err) {
                                if (err.response?.status === 401) {
                                    window.location.href = '/login';
                                }
                            } finally {
                                this.isLoading = false;
                            }
                        },

                        async saveProfile() {
                            this.errors = {};
                            this.profileSuccess = null;
                            this.isSavingProfile = true;
                            try {
                                const res = await apiClient.put('/profile', {
                                    name: this.form.name,
                                    email: this.form.email,
                                    phone: this.form.phone,
                                });
                                Alpine.store('auth').user = res.data.data || Alpine.store('auth').user;
                                this.profileSuccess = 'Profil berhasil diperbarui.';
                                setTimeout(() => { this.profileSuccess = null; }, 3000);
                            } catch (err) {
                                const resp = err.response?.data || {};
                                if (resp.errors) {
                                    for (const key in resp.errors) {
                                        this.errors[key] = Array.isArray(resp.errors[key]) ? resp.errors[key][0] : resp.errors[key];
                                    }
                                }
                                if (resp.message && !resp.errors) {
                                    this.errors.name = resp.message;
                                }
                            } finally {
                                this.isSavingProfile = false;
                            }
                        },

                        async changePassword() {
                            this.pwErrors = {};
                            this.pwSuccess = null;

                            if (!this.pwForm.current_password) {
                                this.pwErrors.current_password = 'Password saat ini wajib diisi.';
                                return;
                            }
                            if (!this.pwForm.new_password || this.pwForm.new_password.length < 8) {
                                this.pwErrors.new_password = 'Password baru minimal 8 karakter.';
                                return;
                            }
                            if (this.pwForm.new_password !== this.pwForm.new_password_confirmation) {
                                this.pwErrors.new_password_confirmation = 'Konfirmasi password tidak cocok.';
                                return;
                            }

                            this.isSavingPw = true;
                            try {
                                await apiClient.put('/profile/password', {
                                    current_password: this.pwForm.current_password,
                                    new_password: this.pwForm.new_password,
                                    new_password_confirmation: this.pwForm.new_password_confirmation,
                                });
                                this.pwSuccess = 'Password berhasil diubah.';
                                this.pwForm = { current_password: '', new_password: '', new_password_confirmation: '' };
                                setTimeout(() => { this.pwSuccess = null; }, 3000);
                            } catch (err) {
                                const resp = err.response?.data || {};
                                if (resp.errors) {
                                    for (const key in resp.errors) {
                                        this.pwErrors[key] = Array.isArray(resp.errors[key]) ? resp.errors[key][0] : resp.errors[key];
                                    }
                                }
                                if (resp.message && !resp.errors) {
                                    this.pwErrors.current_password = resp.message;
                                }
                            } finally {
                                this.isSavingPw = false;
                            }
                        },
                    }));
                });
            </script>
        @endpush
    @endonce
@endsection

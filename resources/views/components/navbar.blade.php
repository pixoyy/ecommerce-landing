<nav class="bg-white border-b border-stone-200" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-6">
                <a href="/" class="text-xl font-bold tracking-tight text-stone-900 shrink-0">
                    EssenseLuxe
                </a>
                <div class="hidden md:block relative">
                    <input type="search" placeholder="Cari produk..."
                           class="w-64 lg:w-80 pl-4 pr-10 py-2 bg-stone-100 border border-stone-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <template x-if="$store.auth.isAuthenticated">
                    <div class="hidden sm:flex items-center gap-3">
                        {{-- Points --}}
                        <a href="{{ route('rewards.index') }}" class="hidden lg:flex items-center gap-1 px-2 py-1 text-xs font-medium text-stone-500 hover:text-stone-900 transition-colors">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="Number($store.auth.user?.point_balance || 0).toLocaleString('id-ID')"></span>
                        </a>
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-stone-600 hover:text-stone-900 transition-colors" aria-label="Keranjang">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-stone-900 text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full"
                                  x-text="$store.cart.totalItemCount" x-show="$store.cart.totalItemCount > 0" aria-live="polite"></span>
                        </a>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-stone-600 hover:text-stone-900 rounded-lg hover:bg-stone-100 transition-colors"
                                    aria-label="Menu pengguna">
                                <span x-text="$store.auth.user?.name || 'User'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-1 w-48 bg-white border border-stone-200 rounded-lg shadow-lg z-50">
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-stone-600 hover:bg-stone-50">Pesanan Saya</a>
                                <a href="{{ route('rewards.index') }}" class="block px-4 py-2 text-sm text-stone-600 hover:bg-stone-50">Poin Reward</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-stone-600 hover:bg-stone-50">Profil</a>
                                <hr class="my-1 border-stone-200">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="!$store.auth.isAuthenticated">
                    <div class="hidden sm:flex items-center gap-3">
                        <a href="/login" class="text-sm font-medium text-stone-600 hover:text-stone-900 transition-colors">Masuk</a>
                        <a href="/register" class="text-sm font-medium bg-stone-900 text-white px-4 py-2 rounded-lg hover:bg-stone-800 transition-colors">Daftar</a>
                    </div>
                </template>

                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-stone-600" aria-label="Buka menu navigasi">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" @click.outside="mobileOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-stone-200 bg-white">
        <div class="px-4 py-3">
            <input type="search" placeholder="Cari produk..."
                   class="w-full px-4 py-2 bg-stone-100 border border-stone-200 rounded-lg text-sm">
        </div>
        <div class="px-4 pb-3 space-y-1">
            <template x-if="$store.auth.isAuthenticated">
                <>
                    <span class="block px-3 py-2 text-sm font-medium text-stone-900" x-text="$store.auth.user?.name"></span>
                    <a href="{{ route('cart.index') }}" class="block px-3 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg">Keranjang</a>
                    <a href="{{ route('orders.index') }}" class="block px-3 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg">Pesanan Saya</a>
                    <a href="{{ route('rewards.index') }}" class="block px-3 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg">Poin Reward</a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Logout</button>
                    </form>
                </>
            </template>
            <template x-if="!$store.auth.isAuthenticated">
                <div class="space-y-1">
                    <a href="/login" class="block px-3 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg">Masuk</a>
                    <a href="/register" class="block px-3 py-2 text-sm text-stone-600 hover:bg-stone-50 rounded-lg">Daftar</a>
                </div>
            </template>
        </div>
    </div>
</nav>

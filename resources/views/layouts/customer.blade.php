@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="lg:w-56 shrink-0">
                <nav class="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-x-visible pb-2 lg:pb-0">
                    <a href="{{ route('orders.index') }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition-colors @yield('orders-active')">
                        Pesanan Saya
                    </a>
                    <a href="{{ route('rewards.index') }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition-colors @yield('rewards-active')">
                        Poin Reward
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition-colors @yield('profile-active')">
                        Profil
                    </a>
                </nav>
            </aside>
            <section class="flex-1 min-w-0">
                @yield('customer-content')
            </section>
        </div>
    </div>
@endsection

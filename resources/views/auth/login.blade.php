@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="w-full max-w-sm">
        <h1 class="text-2xl font-bold text-stone-900 text-center">Masuk</h1>
        <p class="text-sm text-stone-500 text-center mt-1">Selamat datang kembali di EssenseLuxe</p>
        <form class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-stone-700">Email</label>
                <input type="email" id="email" name="email"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-stone-700">Password</label>
                <input type="password" id="password" name="password"
                       class="mt-1 block w-full px-4 py-2.5 bg-white border border-stone-300 rounded-lg text-sm focus:ring-2 focus:ring-stone-900/20 focus:border-stone-900">
            </div>
            <button type="submit"
                    class="w-full bg-stone-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-stone-800 transition-colors">
                Masuk
            </button>
        </form>
        <p class="mt-4 text-center text-sm text-stone-500">
            Belum punya akun? <a href="/register" class="text-stone-900 font-medium hover:underline">Daftar</a>
        </p>
    </div>
@endsection

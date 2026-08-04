@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center">Masuk ke Akun Anda</h2>
    <p class="text-xs text-gray-500 text-center mb-6">Silakan login untuk mengakses form rekomendasi KNN.</p>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-xs rounded-lg p-3 mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-400 focus:outline-none">
        </div>
        <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-medium py-2.5 rounded-lg transition text-sm">
            Login
        </button>
    </form>

    <p class="text-xs text-center text-gray-500 mt-6">
        Belum punya akun? <a href="{{ route('register') }}" class="text-rose-600 font-semibold hover:underline">Daftar sekarang</a>
    </p>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center">Buat Akun Baru</h2>
    <p class="text-xs text-gray-500 text-center mb-6">Daftarkan akun untuk mencari rekomendasi skincare.</p>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-xs rounded-lg p-3 mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-400 focus:outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-rose-400 focus:outline-none">
        </div>
        <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-medium py-2.5 rounded-lg transition text-sm">
            Daftar & Login
        </button>
    </form>

    <p class="text-xs text-center text-gray-500 mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-rose-600 font-semibold hover:underline">Masuk di sini</a>
    </p>
</div>
@endsection
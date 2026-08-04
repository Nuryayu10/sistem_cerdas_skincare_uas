@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Hasil Rekomendasi Skincare</h2>
        <a href="{{ route('recommend.form') }}" class="btn btn-outline-secondary">
            &larr; Cari Ulang / Ubah Input
        </a>
    </div>
  {{-- ================= OUTPUT HASIL KNN ================= --}}
    @if (is_array($results))
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Hasil Rekomendasi ({{ count($results) }} produk)
            </h2>

            @if (empty($results))
                <p class="text-gray-500 text-sm">
                    Tidak ada produk yang cocok dengan kriteria kamu (usia / keamanan bumil-busui / kisaran harga).
                    Coba ubah data input.
                </p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($results as $item)
                        @php $p = $item['product']; @endphp
                        <div class="border border-gray-200 rounded-xl p-4 hover:shadow-lg transition">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-semibold text-rose-500 uppercase">{{ $p['Brand'] }}</span>
                                <span class="text-xs bg-green-100 text-green-700 rounded-full px-2 py-0.5">
                                    {{ $item['match_score'] }}% cocok
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-800">{{ $p['Nama Produk'] }}</h3>
                            <p class="text-sm text-gray-500 mb-2">{{ $p['Jenis Produk'] }} · {{ $p['Ukuran'] }}</p>

                            <div class="text-xs text-gray-600 space-y-1">
                                <p>🧴 Untuk kulit: <span class="font-medium">{{ $p['Untuk Kulit'] }}</span></p>
                                <p>🎯 Mengatasi: <span class="font-medium">{{ $p['Masalah Kulit'] }}</span></p>
                                <p>🧪 Bahan aktif: <span class="font-medium">{{ $p['Tipe Bahan Aktif'] }}</span></p>
                                <p>🤰 Keamanan bumil/busui: <span class="font-medium">{{ $p['Keamanan Bumil & Busui'] }}</span></p>
                                <p>💰 Harga: <span class="font-medium">{{ $p['Kisaran Harga'] }}</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
@endsection
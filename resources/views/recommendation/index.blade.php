@extends('layouts.app')

@section('content')

    {{-- ================= FORM INPUT (UI) ================= --}}
    <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Masukkan Data Kamu</h2>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg p-3 mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

       <form method="POST" action="{{ route('recommend.process') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Usia</label>
                <input type="number" name="usia" min="1" max="100"
                       value="{{ old('usia', $input['usia'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 focus:outline-none"
                       placeholder="contoh: 20" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Kulit</label>
                <select name="jenis_kulit" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 focus:outline-none" required>
                    <option value="">-- Pilih Jenis Kulit --</option>
                    @foreach ($jenisKulitList as $jk)
                        <option value="{{ $jk }}" @selected(old('jenis_kulit', $input['jenis_kulit'] ?? '') === $jk)>{{ $jk }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Masalah Kulit Utama</label>
                <select name="masalah_kulit" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 focus:outline-none" required>
                    <option value="">-- Pilih Masalah Kulit --</option>
                    @foreach ($masalahKulitList as $mk)
                        <option value="{{ $mk }}" @selected(old('masalah_kulit', $input['masalah_kulit'] ?? '') === $mk)>{{ $mk }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Sedang Hamil / Menyusui?</label>
                <select name="hamil_menyusui" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 focus:outline-none" required>
                    <option value="">-- Pilih --</option>
                    <option value="tidak" @selected(old('hamil_menyusui', $input['hamil_menyusui'] ?? '') === 'tidak')>Tidak</option>
                    <option value="ya" @selected(old('hamil_menyusui', $input['hamil_menyusui'] ?? '') === 'ya')>Ya</option>
                </select>
            </div>

            <div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-600 mb-1">Maksimal Anggaran / Budget (Rp) <span class="text-xs text-gray-400">(Opsional)</span></label>
    <input type="number" name="budget" step="1000" min="0"
           value="{{ old('budget', $input['budget'] ?? '') }}"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 focus:outline-none"
           placeholder="contoh: 100000 (kosongkan jika tidak ada batasan)">
</div>

            <div class="md:col-span-2">
                <button type="submit"
                        class="w-full bg-rose-500 hover:bg-rose-600 text-white font-medium py-2.5 rounded-lg transition">
                    Cari Rekomendasi
                </button>
            </div>
        </form>
    </div>

@endsection

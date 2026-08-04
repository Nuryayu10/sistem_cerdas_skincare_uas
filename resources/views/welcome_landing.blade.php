@extends('layouts.app')

@section('content')

<!-- Hero Banner -->
<div class="relative overflow-hidden bg-gradient-to-r from-rose-400 to-pink-500 rounded-3xl p-10 md:p-20 text-white mb-14 shadow-xl text-center md:text-left flex flex-col md:flex-row items-center justify-between min-h-[320px] md:min-h-[420px]">
    <!-- decorative blobs -->
    <div class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-10 w-72 h-72 bg-white/10 rounded-full"></div>

    <div class="relative max-w-2xl">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-5 leading-tight">Temukan Skincare Terbaik Sesuai Jenis & Masalah Kulitmu</h1>
        <p class="text-rose-100 mb-8 text-base md:text-lg">Pahami kondisi kulitmu terlebih dahulu dan dapatkan rekomendasi produk cerdas berbasis algoritma K-Nearest Neighbor (KNN).</p>
        @auth
            <a href="{{ route('recommend.form') }}" class="inline-block bg-white text-rose-600 font-bold px-8 py-4 rounded-xl shadow hover:bg-rose-50 transition text-base md:text-lg">Mulai Cari Rekomendasi &rarr;</a>
        @else
            <a href="{{ route('login') }}" class="inline-block bg-white text-rose-600 font-bold px-8 py-4 rounded-xl shadow hover:bg-rose-50 transition text-base md:text-lg">Login untuk Cari Rekomendasi &rarr;</a>
        @endauth
    </div>
</div>

<!-- Anchor target for "Edukasi" nav link -->
<div id="edukasi" class="scroll-mt-24">

<!-- Section 1: Jenis-Jenis Kulit & Cara Membedakannya -->
<div class="mb-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center">✨ Jenis-Jenis Kulit & Cara Membedakannya</h2>
    <p class="text-gray-500 text-center text-sm mb-6">Lakukan tes cuci muka sederana: Cuci muka, tunggu 30 menit tanpa memakai produk apapun, lalu amati kondisi kulitmu.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
            <img src="{{ asset('images/jenis-kulit/kulit-berminyak.jpg') }}" alt="Kulit Berminyak" class="w-full h-40 object-cover">
            <div class="p-5">
                <h3 class="font-semibold text-rose-600 text-lg mb-2">1. Kulit Berminyak</h3>
                <p class="text-xs text-gray-600 mb-2"><b>Ciri-ciri:</b> Tampak mengkilap di seluruh wajah, pori-pori besar, rentan jerawat dan komedo.</p>
                <p class="text-xs text-gray-500"><b>Penyebab:</b> Produksi sebum berlebih oleh kelenjar minyak.</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
            <img src="{{ asset('images/jenis-kulit/kulit-kering.jpg') }}" alt="Kulit Kering" class="w-full h-40 object-cover">
            <div class="p-5">
                <h3 class="font-semibold text-rose-600 text-lg mb-2">2. Kulit Kering</h3>
                <p class="text-xs text-gray-600 mb-2"><b>Ciri-ciri:</b> Terasa tertarik/kaku, bersisik, kasar, atau kadang terasa gatal.</p>
                <p class="text-xs text-gray-500"><b>Penyebab:</b> Kurangnya kelembapan dan kebiasaan cuci muka air panas.</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
            <img src="{{ asset('images/jenis-kulit/kulit-kombinasi.jpg') }}" alt="Kulit Kombinasi" class="w-full h-40 object-cover">
            <div class="p-5">
                <h3 class="font-semibold text-rose-600 text-lg mb-2">3. Kulit Kombinasi</h3>
                <p class="text-xs text-gray-600 mb-2"><b>Ciri-ciri:</b> Berminyak di area T-Zone (dahi, hidung, dagu), namun kering/normal di pipi.</p>
                <p class="text-xs text-gray-500"><b>Penyebab:</b> Perbedaan distribusi kelenjar sebum di area wajah.</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
            <img src="{{ asset('images/jenis-kulit/kulit-normal.jpg') }}" alt="Kulit Normal" class="w-full h-40 object-cover">
            <div class="p-5">
                <h3 class="font-semibold text-rose-600 text-lg mb-2">4. Kulit Normal</h3>
                <p class="text-xs text-gray-600 mb-2"><b>Ciri-ciri:</b> Seimbang, tidak terlalu berminyak maupun kering, jarang timbul masalah.</p>
                <p class="text-xs text-gray-500"><b>Penyebab:</b> Produksi minyak dan kelembapan alami kulit ideal.</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
                <img src="{{ asset('images/jenis-kulit/kulit-sensitif.jpg') }}" alt="Kulit Sensitif" class="w-full h-40 object-cover">
                <div class="p-5">
                    <h3 class="font-semibold text-rose-600 text-lg mb-2">5. Kulit Sensitif</h3>
                    <p class="text-xs text-gray-600 mb-2"><b>Ciri-ciri:</b> Mudah kemerahan, terasa perih/terbakar saat mencoba produk baru atau terpapar panas.</p>
                    <p class="text-xs text-gray-500"><b>Penyebab:</b> Barrier (lapisan pelindung) kulit rusak/lemah.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Masalah Kulit & Penyebabnya -->
<div class="mb-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🎯 Masalah Kulit Utama & Penyebabnya</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex items-start space-x-3">
            <img src="{{ asset('images/masalah-kulit/jerawat-bekas-jerawat.jpg') }}" alt="Jerawat & Bekas Jerawat" class="w-24 h-24 object-cover shrink-0">
            <div class="py-3 pr-3">
                <h4 class="font-semibold text-gray-800">Jerawat & Bekas Jerawat</h4>
                <p class="text-xs text-gray-500">Penyebab: Pori-pori tersumbat minyak & sel kulit mati yang terinfeksi bakteri C. acnes.</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex items-start space-x-3">
            <img src="{{ asset('images/masalah-kulit/dehidrasi-iritasi.jpg') }}" alt="Dehidrasi & Iritasi" class="w-24 h-24 object-cover shrink-0">
            <div class="py-3 pr-3">
                <h4 class="font-semibold text-gray-800">Dehidrasi & Iritasi</h4>
                <p class="text-xs text-gray-500">Penyebab: Kerusakan skin barrier akibat penggunaan bahan eksfoliasi berlebihan atau cuaca ekstrem.</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex items-start space-x-3">
            <img src="{{ asset('images/masalah-kulit/flek-hitam-kusam.jpg') }}" alt="Flek Hitam & Kusam" class="w-24 h-24 object-cover shrink-0">
            <div class="py-3 pr-3">
                <h4 class="font-semibold text-gray-800">Flek Hitam & Kusam</h4>
                <p class="text-xs text-gray-500">Penyebab: Paparan sinar matahari (UV) tanpa perlindungan Sunscreen serta penumpukan sel kulit mati.</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex items-start space-x-3">
            <img src="{{ asset('images/masalah-kulit/penuaan-dini-pori-besar.jpg') }}" alt="Penuaan Dini & Pori Besar" class="w-24 h-24 object-cover shrink-0">
            <div class="py-3 pr-3">
                <h4 class="font-semibold text-gray-800">Penuaan Dini & Pori Besar</h4>
                <p class="text-xs text-gray-500">Penyebab: Penurunan produksi kolagen alami tubuh serta elastisitas kulit yang mengendur.</p>
            </div>
        </div>
    </div>
</div>

<!-- Section 3: Kandungan / Bahan Aktif Skincare -->
<div class="mb-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🧪 Kandungan Aktif Skincare & Fungsinya</h2>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-xs md:text-sm text-gray-600">
            <thead class="bg-rose-50 text-rose-700 font-semibold border-b border-rose-100">
                <tr>
                    <th class="p-3">Kandungan / Active Ingredient</th>
                    <th class="p-3">Fungsi Utama</th>
                    <th class="p-3">Cocok Untuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="p-3 font-semibold text-gray-800">Salicylic Acid (BHA)</td>
                    <td class="p-3">Membersihkan minyak di dalam pori-pori & meredakan jerawat.</td>
                    <td class="p-3">Kulit Berminyak, Jerawat</td>
                </tr>
                <tr>
                    <td class="p-3 font-semibold text-gray-800">Niacinamide</td>
                    <td class="p-3">Mencerahkan, mengontrol minyak, serta menyamarkan noda hitam.</td>
                    <td class="p-3">Kulit Kusam, Flek Hitam</td>
                </tr>
                <tr>
                    <td class="p-3 font-semibold text-gray-800">Hyaluronic Acid</td>
                    <td class="p-3">Meningkatkan kadar air dan mengunci kelembapan kulit.</td>
                    <td class="p-3">Kulit Kering, Dehidrasi</td>
                </tr>
                <tr>
                    <td class="p-3 font-semibold text-gray-800">Centella Asiatica (Cica)</td>
                    <td class="p-3">Menenangkan kulit kemerahan, perih, atau iritasi.</td>
                    <td class="p-3">Kulit Sensitif, Iritasi</td>
                </tr>
                <tr>
                    <td class="p-3 font-semibold text-gray-800">Retinol</td>
                    <td class="p-3">Merangsang regenerasi sel kulit dan produksi kolagen.</td>
                    <td class="p-3">Penuaan, Pori Besar</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</div>
<!-- /#edukasi -->

<!-- Call to Action -->
<div class="text-center bg-rose-50 p-8 rounded-2xl border border-rose-200">
    <h3 class="text-xl font-bold text-rose-700 mb-2">Sudah Paham Kondisi Kulitmu?</h3>
    <p class="text-xs md:text-sm text-gray-600 mb-4">Masuk ke akunmu untuk menggunakan mesin kecerdasan buatan KNN dan mencari rekomendasi produk skincare yang tepat!</p>
    @auth
        <a href="{{ route('recommend.form') }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold px-6 py-2.5 rounded-xl transition">Masuk ke Form Rekomendasi</a>
    @else
        <a href="{{ route('login') }}" class="bg-rose-500 hover:bg-rose-600 text-white font-bold px-6 py-2.5 rounded-xl transition">Login / Daftar Sekarang</a>
    @endauth
</div>

@endsection
<?php

namespace App\Http\Controllers;

use App\Services\KnnRecommender;
use App\Services\SkincareDataPreprocessor;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    /** Tampilkan form input (Antarmuka Pengguna) */
    public function index()
    {
        return view('recommendation.index', [
            'jenisKulitList' => SkincareDataPreprocessor::JENIS_KULIT,
            'masalahKulitList' => SkincareDataPreprocessor::MASALAH_KULIT,
        ]);
    }

    /** Proses input user -> jalankan KNN -> simpan ke session & redirect ke hasil */
    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'usia' => 'required|integer|min:1|max:100',
            'jenis_kulit' => 'required|in:' . implode(',', SkincareDataPreprocessor::JENIS_KULIT),
            'masalah_kulit' => 'required|in:' . implode(',', SkincareDataPreprocessor::MASALAH_KULIT),
            'hamil_menyusui' => 'required|in:ya,tidak',
            'kisaran_harga' => 'nullable|numeric|min:0',
        ], [
            'usia.required' => 'Usia wajib diisi.',
            'usia.integer' => 'Usia harus berupa angka.',
            'jenis_kulit.required' => 'Jenis kulit wajib dipilih.',
            'masalah_kulit.required' => 'Masalah kulit wajib dipilih.',
            'hamil_menyusui.required' => 'Status hamil/menyusui wajib dipilih.',
            'kisaran_harga.numeric' => 'Kisaran harga harus berupa angka.',
        ]);

        $recommender = new KnnRecommender(new SkincareDataPreprocessor());
        $results = $recommender->recommend($validated, 8);

        // --- PERUBAHAN DI SINI ---
        // Alih-alih merender view langsung, lakukan REDIRECT membawa hasil lewat Session (with)
        return redirect()->route('recommend.result')->with([
            'results' => $results,
            'input'   => $validated,
        ]);
    }

    /** --- METHOD BARU --- Tampilkan halaman hasil rekomendasi */
    public function result()
    {
        // Proteksi jika user mencoba akses /rekomendasi/hasil secara langsung tanpa isi form
        if (!session()->has('results')) {
            return redirect()->route('recommend.form')->with('error', 'Silakan isi formulir terlebih dahulu.');
        }

        // Ambil data hasil rekomendasi dari session
        $results = session('results');
        $input   = session('input');

        return view('recommendation.result', [
            'results' => $results,
            'input'   => $input,
        ]);
    }
}
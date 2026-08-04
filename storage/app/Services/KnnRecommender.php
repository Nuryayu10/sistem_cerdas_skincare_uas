<?php

namespace App\Services;

/**
 * KnnRecommender
 * ---------------
 * MESIN CERDAS: implementasi algoritma K-Nearest Neighbor (KNN) murni PHP
 * (tanpa library eksternal) untuk merekomendasikan produk skincare.
 *
 * Alur:
 *  A. Preprocessing dataset (clean -> encode -> normalize)
 *  B. Filter aturan bisnis wajib (usia minimal, keamanan bumil/busui)
 *     -> ini BUKAN bagian algoritma KNN, tapi syarat keamanan produk
 *        yang tidak boleh dilanggar meskipun jaraknya dekat secara fitur.
 *  C. Bangun vektor fitur input user dengan encoding yang SAMA seperti data produk
 *  D. Hitung jarak Euclidean antara vektor user vs setiap produk (KNN)
 *  E. Urutkan dari jarak terkecil -> ambil K produk teratas sebagai rekomendasi
 */
class KnnRecommender
{
    public function __construct(protected SkincareDataPreprocessor $preprocessor)
    {
    }

    /**
     * @param array $userInput ['usia'=>int,'jenis_kulit'=>string,'masalah_kulit'=>string,'hamil_menyusui'=>'ya'|'tidak']
     * @param int $k jumlah tetangga terdekat / jumlah rekomendasi yang ditampilkan
     */
    public function recommend(array $userInput, int $k = 8): array
    {
        // ---- A. PREPROCESSING ----
        $this->preprocessor->load(storage_path('app/skincare.csv'))->clean();
        $encoded = $this->preprocessor->encode();
        $encoded = $this->preprocessor->normalize($encoded);

        // ---- B. FILTER ATURAN BISNIS WAJIB ----
        $usia = (int) $userInput['usia'];
        $hamilMenyusui = $userInput['hamil_menyusui'] === 'ya';

        $filtered = array_values(array_filter($encoded, function ($item) use ($usia, $hamilMenyusui) {
            if ($usia < $item['min_age']) {
                return false; // di bawah batas usia minimal produk
            }
            if ($hamilMenyusui && $item['safety_score'] < 1.0) {
                return false; // hanya produk berlabel "Aman" yang boleh muncul
            }
            return true;
        }));

        if (empty($filtered)) {
            return [];
        }

        // ---- C. VEKTOR FITUR USER (encoding sama seperti produk) ----
        $userSkinVector = array_map(
            fn ($c) => $c === $userInput['jenis_kulit'] ? 1 : 0,
            SkincareDataPreprocessor::JENIS_KULIT
        );
        $userProblemVector = array_map(
            fn ($c) => $c === $userInput['masalah_kulit'] ? 1 : 0,
            SkincareDataPreprocessor::MASALAH_KULIT
        );
        $userAgeNorm = $this->minMax($usia, SkincareDataPreprocessor::USIA_MIN, SkincareDataPreprocessor::USIA_MAX);
        $userVector = array_merge($userSkinVector, $userProblemVector, [$userAgeNorm]);

        // ---- D. HITUNG JARAK EUCLIDEAN (KNN) ----
        $distances = [];
        foreach ($filtered as $item) {
            $productVector = array_merge($item['skin_type_vector'], $item['problem_vector'], [$item['min_age_norm']]);
            $distances[] = [
                'distance' => $this->euclidean($userVector, $productVector),
                'match_score' => $this->toMatchScore($this->euclidean($userVector, $productVector)),
                'product' => $item['raw'],
            ];
        }

        // ---- E. URUTKAN & AMBIL TOP-K ----
        usort($distances, fn ($a, $b) => $a['distance'] <=> $b['distance']);

        return array_slice($distances, 0, $k);
    }

    protected function euclidean(array $a, array $b): float
    {
        $sum = 0.0;
        foreach ($a as $i => $val) {
            $sum += ($val - $b[$i]) ** 2;
        }
        return sqrt($sum);
    }

    /** Ubah jarak Euclidean jadi persentase kecocokan yang mudah dibaca user (0-100%) */
    protected function toMatchScore(float $distance): float
    {
        // jarak maksimum teoritis pada ruang fitur ini sekitar sqrt(4) = 2.0
        $maxDistance = 2.0;
        $score = (1 - min($distance, $maxDistance) / $maxDistance) * 100;
        return round($score, 1);
    }

    protected function minMax(float|int $value, float|int $min, float|int $max): float
    {
        if ($max === $min) {
            return 0.0;
        }
        return max(0, min(1, ($value - $min) / ($max - $min)));
    }
}

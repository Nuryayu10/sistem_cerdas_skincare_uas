<?php

namespace App\Services;

class KnnRecommender
{
    public function __construct(protected SkincareDataPreprocessor $preprocessor)
    {
    }

    /**
     * @param array $userInput ['usia'=>int, 'jenis_kulit'=>string, 'masalah_kulit'=>string, 'hamil_menyusui'=>'ya'|'tidak', 'budget'=>int|null]
     * @param int $k
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
        $budget = !empty($userInput['budget']) ? (int) $userInput['budget'] : null;

        // Note: Variabel $budget dimasukkan ke dalam use ($usia, $hamilMenyusui, $budget)
        $filtered = array_values(array_filter($encoded, function ($item) use ($usia, $hamilMenyusui, $budget) {
            if ($usia < $item['min_age']) {
                return false;
            }
            if ($hamilMenyusui && $item['safety_score'] < 1.0) {
                return false;
            }
            // Filter Budget: Jika user mengisi budget, tampilkan produk yang harganya <= budget
            if ($budget !== null && isset($item['price']) && $item['price'] > 0 && $item['price'] > $budget) {
                return false;
            }
            return true;
        }));

        if (empty($filtered)) {
            return [];
        }

        // ---- C. VEKTOR FITUR USER ----
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

    protected function toMatchScore(float $distance): float
    {
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
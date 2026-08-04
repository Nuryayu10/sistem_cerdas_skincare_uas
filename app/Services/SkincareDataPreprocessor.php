<?php

namespace App\Services;

/**
 * SkincareDataPreprocessor
 * ------------------------
 * Menjalankan tahap PEMROSESAN DATA sesuai pipeline mini proyek:
 * 1. Cleaning     -> buang baris dengan kolom penting kosong
 * 2. Encoding     -> ubah kategori teks (Jenis Kulit, Masalah Kulit, Keamanan Bumil/Busui) jadi numerik
 * 3. Normalisasi  -> min-max scaling untuk fitur numerik (Usia)
 *
 * Dataset dibaca langsung dari storage/app/skincare.csv (tidak perlu database).
 */
class SkincareDataPreprocessor
{
    // Kategori tetap hasil observasi dataset (dipakai untuk one-hot encoding)
    public const JENIS_KULIT = ['Berminyak', 'Kering', 'Kombinasi', 'Normal', 'Sensitif'];

    public const MASALAH_KULIT = [
        'Bekas jerawat', 'Dehidrasi', 'Flek hitam', 'Iritasi',
        'Jerawat', 'Kusam', 'Penuaan', 'Pori besar',
    ];

    public const USIA_MIN = 11;
    public const USIA_MAX = 20;

    protected array $rawData = [];
    protected array $cleanData = [];

    /** Baca file CSV mentah ke array asosiatif */
    public function load(string $csvPath): self
    {
        $this->rawData = [];
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                continue; // lewati baris rusak/tidak lengkap
            }
            $this->rawData[] = array_combine($header, $row);
        }
        fclose($handle);

        return $this;
    }

    /** STEP 1 - CLEANING: buang baris dengan field penting kosong/null */
    public function clean(): self
    {
        $required = ['Untuk Kulit', 'Masalah Kulit', 'Batas Usia Minimal', 'Keamanan Bumil & Busui', 'Kisaran Harga'];

        $this->cleanData = array_values(array_filter($this->rawData, function ($row) use ($required) {
            foreach ($required as $col) {
                if (!isset($row[$col]) || trim($row[$col]) === '') {
                    return false;
                }
            }
            return true;
        }));

        return $this;
    }

    /** STEP 2 - ENCODING: teks kategori -> vektor numerik (one-hot) + skor keamanan */
    public function encode(): array
    {
        $encoded = [];

        foreach ($this->cleanData as $i => $row) {
            $encoded[] = [
                'id' => $i,
                'raw' => $row,
                'skin_type_vector' => $this->oneHot(self::JENIS_KULIT, $row['Untuk Kulit']),
                'problem_vector' => $this->oneHot(self::MASALAH_KULIT, $row['Masalah Kulit']),
                'min_age' => (int) filter_var($row['Batas Usia Minimal'], FILTER_SANITIZE_NUMBER_INT),
                'safety_score' => $this->safetyScore($row['Keamanan Bumil & Busui']),
                'price_range' => $row['Kisaran Harga'],
            ];
        }

        return $encoded;
    }

    /** STEP 3 - NORMALISASI: min-max scaling untuk usia minimal produk */
    public function normalize(array $encoded): array
    {
        foreach ($encoded as &$item) {
            $item['min_age_norm'] = $this->minMax($item['min_age'], self::USIA_MIN, self::USIA_MAX);
        }

        return $encoded;
    }

    protected function oneHot(array $categories, string $value): array
    {
        $value = trim($value);
        return array_map(fn ($c) => $c === $value ? 1 : 0, $categories);
    }

    protected function safetyScore(string $value): float
    {
        return match (trim($value)) {
            'Aman' => 1.0,
            'Hati-hati (Konsultasi Dokter)' => 0.5,
            'Tidak Aman (Dilarang Keras)' => 0.0,
            default => 0.0,
        };
    }

    protected function minMax(float|int $value, float|int $min, float|int $max): float
    {
        if ($max === $min) {
            return 0.0;
        }
        return max(0, min(1, ($value - $min) / ($max - $min)));
    }

    public function getCleanData(): array
    {
        return $this->cleanData;
    }
}

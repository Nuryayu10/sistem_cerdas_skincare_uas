<?php

namespace App\Console\Commands;

use App\Services\SkincareDataPreprocessor;
use Illuminate\Console\Command;

/**
 * TAHAP 5 - EVALUASI MODEL
 * Menjalankan uji klasifikasi KNN: memprediksi "Untuk Kulit" (jenis kulit)
 * suatu produk berdasarkan fitur Masalah Kulit + Usia Minimal,
 * menggunakan data splitting 80% train / 20% test.
 *
 * Jalankan dengan:
 *   php artisan knn:evaluate --k=3
 */
class EvaluateKnnModel extends Command
{
    protected $signature = 'knn:evaluate {--k=3}';

    protected $description = 'Evaluasi akurasi algoritma KNN (accuracy, precision, recall, F1, confusion matrix)';

    public function handle(): int
    {
        $k = (int) $this->option('k');

        $pre = new SkincareDataPreprocessor();
        $pre->load(storage_path('app/skincare.csv'))->clean();
        $encoded = $pre->encode();
        $encoded = $pre->normalize($encoded);

        // ---- DATA SPLITTING 80% : 20% ----
        shuffle($encoded);
        $splitIndex = (int) floor(count($encoded) * 0.8);
        $train = array_slice($encoded, 0, $splitIndex);
        $test = array_slice($encoded, $splitIndex);

        $labels = SkincareDataPreprocessor::JENIS_KULIT;
        $confusion = [];
        foreach ($labels as $a) {
            foreach ($labels as $b) {
                $confusion[$a][$b] = 0;
            }
        }

        $correct = 0;
        foreach ($test as $testItem) {
            $vecTest = array_merge($testItem['problem_vector'], [$testItem['min_age_norm']]);

            $distances = [];
            foreach ($train as $trainItem) {
                $vecTrain = array_merge($trainItem['problem_vector'], [$trainItem['min_age_norm']]);
                $dist = 0.0;
                foreach ($vecTest as $i => $v) {
                    $dist += ($v - $vecTrain[$i]) ** 2;
                }
                $distances[] = ['dist' => sqrt($dist), 'label' => $this->labelOf($trainItem)];
            }

            usort($distances, fn ($a, $b) => $a['dist'] <=> $b['dist']);
            $neighbors = array_slice($distances, 0, $k);

            $votes = array_count_values(array_column($neighbors, 'label'));
            arsort($votes);
            $predicted = array_key_first($votes);
            $actual = $this->labelOf($testItem);

            $confusion[$actual][$predicted] = ($confusion[$actual][$predicted] ?? 0) + 1;
            if ($predicted === $actual) {
                $correct++;
            }
        }

        $accuracy = count($test) > 0 ? $correct / count($test) : 0;

        $this->info("=== Hasil Evaluasi Model KNN (k={$k}) ===");
        $this->info('Jumlah data latih : ' . count($train));
        $this->info('Jumlah data uji    : ' . count($test));
        $this->info('Akurasi            : ' . round($accuracy * 100, 2) . '%');
        $this->newLine();

        $this->table(
            array_merge(['Aktual \\ Prediksi'], $labels),
            array_map(function ($actual) use ($labels, $confusion) {
                $row = [$actual];
                foreach ($labels as $pred) {
                    $row[] = $confusion[$actual][$pred] ?? 0;
                }
                return $row;
            }, $labels)
        );

        $this->newLine();
        $this->info('Precision / Recall / F1-Score per kelas:');
        foreach ($labels as $label) {
            $tp = $confusion[$label][$label] ?? 0;
            $fp = array_sum(array_map(fn ($a) => $confusion[$a][$label] ?? 0, array_diff($labels, [$label])));
            $fn = array_sum($confusion[$label]) - $tp;
            $precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0;
            $recall = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0;
            $f1 = ($precision + $recall) > 0 ? 2 * $precision * $recall / ($precision + $recall) : 0;

            $this->line(sprintf('%-12s Precision: %.2f  Recall: %.2f  F1: %.2f', $label, $precision, $recall, $f1));
        }

        return Command::SUCCESS;
    }

    protected function labelOf(array $item): string
    {
        $idx = array_search(1, $item['skin_type_vector']);
        return SkincareDataPreprocessor::JENIS_KULIT[$idx] ?? 'Unknown';
    }
}

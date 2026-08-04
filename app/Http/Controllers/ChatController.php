<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $history = ChatMessage::where('user_id', $request->user()->id)
            ->orderBy('created_at')
            ->get();

        return view('chat.index', [
            'history' => $history,
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan terlalu panjang.',
        ]);

        ChatMessage::create([
            'user_id' => $request->user()->id,
            'sender' => 'user',
            'content' => $validated['message'],
        ]);

        $assistantText = $this->generateChatResponse($request->user()->id, $validated['message']);

        ChatMessage::create([
            'user_id' => $request->user()->id,
            'sender' => 'assistant',
            'content' => $assistantText,
        ]);

        return redirect()->route('chat.index');
    }

    protected function generateChatResponse(int $userId, string $message): string
    {
        $openAiKey = config('services.openai.key');

        if ($this->shouldUseOpenAI($openAiKey)) {
            $aiResponse = $this->callOpenAI($userId);
            if ($aiResponse !== null) {
                return $aiResponse;
            }
        }

        return $this->generateLocalAnswer($message);
    }

    protected function shouldUseOpenAI(?string $key): bool
    {
        return !empty($key)
            && str_starts_with($key, 'sk-')
            && $key !== 'your-openai-api-key-here';
    }

    protected function callOpenAI(int $userId): ?string
    {
        $openAiKey = config('services.openai.key');
        $history = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at')
            ->get();

        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah asisten skincare yang ramah dan membantu. Jawab pertanyaan dalam bahasa Indonesia dengan jelas dan singkat.',
            ],
        ];

        foreach ($history->slice(-10) as $item) {
            $messages[] = [
                'role' => $item->sender === 'user' ? 'user' : 'assistant',
                'content' => $item->content,
            ];
        }

        $response = Http::withToken($openAiKey)
            ->accept('application/json')
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 600,
            ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json('choices.0.message.content');
    }

    protected function generateLocalAnswer(string $message): string
    {
        $text = strtolower($message);

        if ($this->containsAny($text, ['acne pack', 'acne pack bagus', 'acne pack baik', 'jerawat', 'perawatan jerawat'])) {
            return 'Acne pack dapat membantu meredakan jerawat dan menenangkan kulit. Pilih formula yang lembut, bebas alkohol, dan tidak menyumbat pori-pori.';
        }

        if ($this->containsAny($text, ['sunscreen', 'tabir surya', 'spf', 'sunblock', 'melindungi kulit'])) {
            return 'Tabir surya penting setiap hari untuk melindungi kulit dari sinar UV. Gunakan minimal SPF 30, ulangi setiap 2-3 jam jika terkena sinar matahari langsung.';
        }

        if ($this->containsAny($text, ['pelembap', 'moisturizer', 'hydration', 'kulit kering', 'kering'])) {
            return 'Pelembap membantu menjaga kelembapan dan memperbaiki lapisan pelindung kulit. Untuk kulit kering, pilih pelembap dengan ceramide atau hyaluronic acid.';
        }

        if ($this->containsAny($text, ['kulit berminyak', 'berminyak', 'kulit oily', 'minyak', 'muka berminyak'])) {
            return 'Untuk kulit berminyak, pilih produk ringan seperti gel cleanser, toner yang menyegarkan, dan pelembap oil-free. Hindari produk yang terlalu berat atau mengandung minyak mineral.';
        }

        if ($this->containsAny($text, ['kulit sensitif', 'sensitif', 'iritasi', 'meradang', 'merah'])) {
            return 'Kulit sensitif mudah bereaksi terhadap produk baru. Pilih formula bebas pewangi dan alkohol, dan lakukan patch test sebelum menggunakan secara penuh.';
        }

        if ($this->containsAny($text, ['eksfoliasi', 'scrub', 'peeling', 'skincare rutin', 'regenerasi'])) {
            return 'Eksfoliasi membantu mengangkat sel kulit mati, tetapi jangan terlalu sering. Untuk kulit sensitif, pilih chemical exfoliator ringan seperti AHA atau BHA satu atau dua kali seminggu.';
        }

        if ($this->containsAny($text, ['jenis kulit', 'kulit kombinasi', 'kulit normal', 'kulit kering', 'tips kulit'])) {
            return 'Jenis kulit meliputi berminyak, kering, kombinasi, sensitif, dan normal. Amati respons kulit terhadap produk dan kondisi setelah bangun tidur untuk menentukan tipe kulitmu.';
        }

        if ($this->containsAny($text, ['toner', 'serum', 'essence', 'sheet mask', 'masker'])) {
            return 'Toner atau serum bisa membantu menyeimbangkan pH dan menambah nutrisi. Serum dengan niacinamide, vitamin C, atau hyaluronic acid sering dipilih sesuai kebutuhan kulit.';
        }

        if ($this->containsAny($text, ['retinol', 'vitamin c', 'niacinamide', 'salicylic acid', 'hyaluronic acid'])) {
            return 'Bahan aktif seperti retinol, vitamin C, niacinamide, dan hyaluronic acid memiliki manfaat berbeda. Gunakan sesuai tujuan: anti-aging, pencerah, kontrol minyak, atau hidrasi.';
        }

        if ($this->containsAny($text, ['pembersih', 'cleanser', 'cuci muka', 'membersihkan wajah', 'facial wash'])) {
            return 'Membersihkan wajah adalah langkah pertama penting. Pilih pembersih lembut yang sesuai jenis kulit dan hindari air terlalu panas agar kulit tidak kering.';
        }

        if ($this->containsAny($text, ['rutinitas pagi', 'pagi', 'malam', 'rutinitas malam', 'routine', 'langkah'])) {
            return 'Rutinitas skincare pagi biasanya: pembersih, toner/essence, serum, pelembap, dan sunscreen. Malam bisa ditambahkan serum khusus, retinol, atau masker perawatan.';
        }

        if ($this->containsAny($text, ['rem', 'remaja', 'remaja', 'anak muda', 'skincare remaja'])) {
            return 'Skincare remaja sebaiknya sederhana: pembersih lembut, pelembap ringan, dan sunscreen. Jika ada jerawat, gunakan produk non-komedogenik dengan salicylic acid.';
        }

        if ($this->containsAny($text, ['pria', 'skincare pria', 'cowok', 'laki-laki'])) {
            return 'Skincare untuk pria sama dasarnya: bersihkan wajah, gunakan pelembap, dan lindungi dengan sunscreen. Pilih produk yang cepat meresap dan tidak terlalu lengket.';
        }

        if ($this->containsAny($text, ['anti aging', 'penuaan', 'keriput', 'anti-aging', 'age'])) {
            return 'Untuk anti-aging, gunakan produk dengan retinol, vitamin C, dan pelembap yang mendukung barrier kulit. Konsisten menggunakan sunscreen juga sangat penting.';
        }

        if ($this->containsAny($text, ['preventif', 'mencegah', 'anti penuaan', 'preventif', 'perlindungan'])) {
            return 'Untuk pencegahan, fokus pada sunscreen, hidrasi, dan menjaga pola hidup sehat. Hindari merokok, cukupi tidur, dan pilih produk yang tidak mengiritasi kulit.';
        }

        if ($this->containsAny($text, ['alergi', 'reaksi alergi', 'gatal', 'bercahaya', 'kemerahan', 'alergi skincare'])) {
            return 'Jika muncul gatal atau kemerahan, hentikan penggunaan produk tersebut. Pilih skincare hypoallergenic dan lakukan patch test terlebih dahulu.';
        }

        if ($this->containsAny($text, ['berapa kali', 'berapa sering', 'dalam sehari']) && $this->containsAny($text, ['sunscreen', 'tabir surya', 'spf', 'sunblock'])) {
            return 'Sunscreen sebaiknya digunakan setiap pagi dan diulang setiap 2-3 jam saat terpapar sinar matahari langsung. Jika banyak berkeringat atau berenang, aplikasikan ulang setelah aktivitas tersebut.';
        }

        if ($this->containsAny($text, ['sunscreen untuk kulit berminyak', 'sunscreen oily', 'sunscreen berminyak', 'sunscreen oil free'])) {
            return 'Untuk kulit berminyak, pilih sunscreen oil-free atau gel-based. Produk dengan tekstur ringan dan non-komedogenik biasanya lebih nyaman digunakan.';
        }

        if ($this->containsAny($text, ['bahan aktif', 'kombinasi bahan', 'tidak boleh', 'jangan dicampur', 'reaksi'])) {
            return 'Hindari mencampurkan retinol dengan vitamin C atau AHA/BHA dalam satu waktu. Gunakan bahan aktif secara bergantian atau sesuai petunjuk produk untuk mengurangi iritasi.';
        }

        if ($this->containsAny($text, ['kulit kombinasi', 'kombinasi'])) {
            return 'Kulit kombinasi biasanya berminyak di T-zone dan kering di pipi. Gunakan produk yang seimbang: pelembap ringan di area berminyak dan hidrasi tambahan di area kering.';
        }

        if ($this->containsAny($text, ['kulit kering', 'kering ekstrim', 'kering banget'])) {
            return 'Kulit kering ekstrim butuh pembersih tanpa sulfat, serum hyaluronic acid, dan pelembap kaya ceramide. Gunakan juga facial oil jika perlu untuk menambah lapisan hidrasi.';
        }

        if ($this->containsAny($text, ['sensitif', 'kulit sensitif', 'mudah iritasi', 'reaksi cepat', 'kulit mudah merah'])) {
            return 'Untuk kulit sensitif, gunakan produk bebas pewangi, alkohol, dan alkohol denat. Lakukan patch test dan pilih bahan lembut seperti centella asiatica atau ceramide.';
        }

        if ($this->containsAny($text, ['double cleansing', 'pembersihan ganda', 'cleansing oil', 'cleansing balm'])) {
            return 'Double cleansing cocok jika memakai makeup atau sunscreen berat: mulai dengan cleansing oil/balm, lalu lanjutkan dengan pembersih berbusa lembut untuk membersihkan sisa kotoran.';
        }

        if ($this->containsAny($text, ['toner', 'essence', 'mist', 'kegunaan toner'])) {
            return 'Toner atau essence membantu mengembalikan pH kulit dan menambah hidrasi sebelum serum. Pilih formula ringan tanpa alkohol jika kulitmu sensitif.';
        }

        if ($this->containsAny($text, ['bekas luka jerawat', 'bekas jerawat', 'scar', 'tekstur tidak merata'])) {
            return 'Untuk bekas jerawat, bahan seperti AHA/BHA ringan, niacinamide, dan vitamin C dapat membantu menghaluskan kulit. Jangan gunakan eksfoliator yang terlalu keras saat kulit sedang iritasi.';
        }

        if ($this->containsAny($text, ['niacinamide', 'hyaluronic acid', 'peptides', 'ceramide', 'vitamin c'])) {
            return 'Niacinamide dan hyaluronic acid biasanya aman dipakai bersama, vitamin C cocok di pagi hari, dan retinol lebih baik dipakai malam hari. Ceramide membantu memperbaiki barrier kulit.';
        }

        if ($this->containsAny($text, ['jerawat', 'tips jerawat', 'menghilangkan jerawat'])) {
            return 'Untuk jerawat, gunakan pembersih lembut, produk dengan bahan seperti salicylic acid atau benzoyl peroxide, dan hindari memencet jerawat agar tidak meninggalkan bekas.';
        }

        if ($this->containsAny($text, ['flek', 'noda', 'hyperpigmentation', 'bekas jerawat', 'bekas'])) {
            return 'Untuk noda hitam dan bekas jerawat, bahan seperti vitamin C, niacinamide, dan AHA/BHA ringan bisa membantu. Selalu kombinasikan dengan tabir surya.';
        }

        if ($this->containsAny($text, ['skincare murah', 'budget', 'murah', 'terjangkau'])) {
            return 'Untuk skincare terjangkau, pilih produk yang fokus pada kebutuhan utama: pembersih lembut, pelembap ringan, dan sunscreen. Bahan seperti niacinamide dan hyaluronic acid sering tersedia di produk harga terjangkau.';
        }

        return 'Maaf, jawaban saya belum lengkap untuk pertanyaan itu. Coba tanyakan tentang sunscreen, pelembap, jenis kulit, retinol, atau rutinitas skincare.';
    }

    protected function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}

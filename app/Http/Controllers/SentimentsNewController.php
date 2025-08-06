<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sastrawi\Stemmer\StemmerFactory;
use Illuminate\Support\Facades\Storage;

class SentimentsNewController extends Controller
{
    private $stemmer;

    public function __construct()
    {
        $this->middleware('auth');

        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $this->normalizationDictionary = [
            'bgus' => 'bagus',
            'gk' => 'tidak',
            'gak' => 'tidak',
            'tp' => 'tapi',
            'sy' => 'saya',
            'dngn' => 'dengan',
            'knp' => 'kenapa',
            'sm' => 'sama',
            'lgi' => 'lagi',
            'dpt' => 'dapat',
            'ga' => 'tidak',
            'gajadi' => 'tidak jadi',
            'yuk' => 'ayo',
            'blm' => 'belum',
            'bgt' => 'banget',
            'nggak' => 'tidak',
            'kpn' => 'kapan',
            'aja' => 'saja',
            'jd' => 'jadi',
            'bs' => 'bisa',
            'gmn' => 'bagaimana',
            'dh' => 'sudah',
            'cm' => 'cuma',
            'btw' => 'by the way',
            'bb' => 'bukan',
            'krn' => 'karena',
            'bln' => 'bulan',
            'tdk' => 'tidak',
            'hrus' => 'harus',
            'gmn' => 'bagaimana',
            'tmn' => 'teman',
            'plg' => 'pulang',
            'gpp' => 'tidak apa apa',
            'jln' => 'jalan',
            'lbh' => 'lebih',
            'tpi' => 'tapi',
            'jg' => 'juga',
        ];

        $this->stopwords = ['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk', 'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', 'yg', 'tapi', 'nya', 'dengan', 'saat', 'lebih', 'tersebut', 'sudah',
            'belum', 'karena', 'saya', 'kami', 'anda', 'mereka', 'terjadi', 'akan', 'sebagai', 'seperti', 'tersebut','hingga', 'untuk', 'pada', 'untuk', 'dalam', 'yang', 'itu', 'disini', 'ada', 'sekarang', 'semua', 'maupun',
            'buat', 'bisa', 'jika', 'setelah', 'kemudian', 'sedang', 'berikut', 'langsung', 'hingga', 'lagi', 'kenapa','apa', 'begitu', 'siapa', 'tidak', 'kita', 'itu', 'dia', 'kak', 'ya', 'beliau', 'tadi', 'baru', 'terlalu',
            'harus', 'mau', 'bagi', 'semuanya', 'sama', 'meskipun', 'ketika', 'jadi', 'sehingga', 'lebih', 'untuk','pasti', 'seperti', 'terus', 'mengapa', 'melalui', 'apalagi', 'hingga', 'adapun', 'apakah', 'padahal', 'bukan'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sentiment-new/index');
    }

    public function createModel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        if (!$request->hasFile('file')) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        try {
            $file = $request->file('file');
            $raw = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_map('trim', $raw[0]);
            unset($raw[0]);

            $dataset = [];
            foreach ($raw as $row) {
                $row = array_combine($header, $row);
                if (!empty($row['Teks']) && !empty($row['Sentimen'])) {
                    $dataset[] = [
                        'text' => $row['Teks'],
                        'label' => $row['Sentimen']
                    ];
                }
            }

            if (empty($dataset)) {
                return back()->with('error', 'Dataset kosong atau format tidak sesuai.');
            }

            // Split data: 80% training, 20% testing
            shuffle($dataset);
            $splitIndex = (int)(count($dataset) * 0.8);
            $trainSet = array_slice($dataset, 0, $splitIndex);
            $testSet = array_slice($dataset, $splitIndex);

            // Training
            $classes = [];
            $classDocCounts = [];
            $classWordCounts = [];
            $vocab = [];

            foreach ($trainSet as $item) {
                $label = $item['label'];
                $words = $this->preprocessing($item['text']);

                $classDocCounts[$label] = ($classDocCounts[$label] ?? 0) + 1;
                if (!isset($classes[$label])) {
                    $classes[$label] = [];
                    $classWordCounts[$label] = 0;
                }

                foreach ($words as $word) {
                    if (trim($word) === '') continue;
                    $classes[$label][$word] = ($classes[$label][$word] ?? 0) + 1;
                    $classWordCounts[$label]++;
                    $vocab[$word] = true;
                }
            }

            $totalDocs = count($trainSet);
            $vocabSize = count($vocab);

            // Prior probabilities
            $priors = [];
            foreach ($classDocCounts as $label => $count) {
                $priors[$label] = $count / $totalDocs;
            }

            // Likelihoods with Laplace smoothing
            $likelihoods = [];
            foreach ($classes as $label => $wordCounts) {
                foreach ($vocab as $word => $_) {
                    $count = $wordCounts[$word] ?? 0;
                    $likelihoods[$label][$word] = ($count + 1) / ($classWordCounts[$label] + $vocabSize);
                }
            }

            // Save model
            $model = [
                'priors' => $priors,
                'likelihoods' => $likelihoods,
                'vocab' => array_keys($vocab),
                'class_word_counts' => $classWordCounts,
                'class_doc_counts' => $classDocCounts,
                'total_docs' => $totalDocs
            ];
            // dd($model);
            Storage::disk('local')->put('model.json', json_encode($model, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Evaluation
            $correct = 0;
            foreach ($testSet as $item) {
                $words = $this->preprocessing($item['text']);
                $scores = [];

                foreach ($priors as $label => $prior) {
                    $score = log($prior);
                    foreach ($words as $word) {
                        if (isset($likelihoods[$label][$word])) {
                            $score += log($likelihoods[$label][$word]);
                        } else {
                            // Laplace smoothing for unseen words
                            $score += log(1 / ($classWordCounts[$label] + $vocabSize));
                        }
                    }
                    $scores[$label] = $score;
                }

                $predicted = array_keys($scores, max($scores))[0];
                if ($predicted === $item['label']) {
                    $correct++;
                }
            }

            $accuracy = $correct / count($testSet);
            return back()->with('success', 'Model berhasil dilatih dan disimpan! Akurasi: ' . round($accuracy * 100, 2) . '%');
        } catch (\Throwable $th) {
            return back()->with('error', 'Gagal memproses file: ' . $th->getMessage());
        }
    }

    public function preprocessing($text)
    {
        // Lowercase
        $text = strtolower($text);
        // Hilangkan simbol, angka, dan karakter non-huruf
        $text = preg_replace('/[^a-z\s]/', '', $text);
        // Tokenisasi
        $tokens = explode(' ', $text);
        $cleaned = [];
        foreach ($tokens as $word) {
            $word = trim($word);
            // Normalisasi slang
            if (isset($this->normalizationDictionary[$word])) {
                $word = $this->normalizationDictionary[$word];
            }
            // Hilangkan huruf berulang (mantap[ppppp])
            $word = preg_replace('/(\w)\1{2,}/', '$1', $word);
            //Filter panjang kata
            if (strlen($word) < 3 || strlen($word) > 15) continue;
            //Stopword removal
            if (in_array($word, $this->stopwords)) continue;
            // Stemming
            $word = $this->stemmer->stem($word);
            // Final filter
            if ($word !== '') {
                $cleaned[] = $word;
            }
        }

        return $cleaned;
    }


    public function sentimenTeks()
    {
        return view('sentiment-new/teks');
    }

    public function analisis(Request $request)
    {
        $request->validate([
            'input_text' => 'required|string'
        ]);

        // Ambil model
        $modelJson = Storage::disk('local')->get('model.json');
        $model = json_decode($modelJson, true);

        $priors = $model['priors'];
        $likelihoods = $model['likelihoods'];
        $classWordCounts = $model['class_word_counts'];
        $vocabSize = count($model['vocab']);

        // Preprocessing input
        $words = $this->preprocessing($request->input('input_text'));

        // Hitung skor probabilitas untuk setiap kelas
        $scores = [];
        foreach ($priors as $label => $prior) {
            $score = log($prior);
            foreach ($words as $word) {
                if (isset($likelihoods[$label][$word])) {
                    $score += log($likelihoods[$label][$word]);
                } else {
                    // Laplace smoothing for unseen words
                    $score += log(1 / ($classWordCounts[$label] + $vocabSize));
                }
            }
            $scores[$label] = $score;
        }

        // Softmax untuk probabilitas
        $expScores = array_map(fn($s) => exp($s), $scores);
        $sumExp = array_sum($expScores);
        $probabilities = [];
        foreach ($expScores as $label => $val) {
            $probabilities[$label] = $val / $sumExp;
        }

        // Prediksi label
        $predictedLabel = array_keys($probabilities, max($probabilities))[0];

        return back()->with('result', [
            'label' => $predictedLabel,
            'probabilities' => $probabilities
        ]);
    }

    public function sentimenFile()
    {
        return view('sentiment-new.file');
    }

    public function analisisFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv'
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $raw = array_map('str_getcsv', file($file->getRealPath()));
                $header = array_map('trim', $raw[0]);
                unset($raw[0]);

                // Ambil teks dari kolom "Teks"
                $dataAnalisis = [];
                foreach ($raw as $row) {
                    $row = array_combine($header, $row);
                    if (!empty($row['Teks'])) {
                        $dataAnalisis[] = $row['Teks'];
                    }
                }

                // Ambil model
                $modelJson = Storage::disk('local')->get('model.json');
                $model = json_decode($modelJson, true);

                $priors = $model['priors'];
                $likelihoods = $model['likelihoods'];
                $classWordCounts = $model['class_word_counts'];
                $vocabSize = count($model['vocab']);

                $allPred = [];
                $allProb = [];

                foreach ($dataAnalisis as $text) {
                    $words = $this->preprocessing($text);

                    $scores = [];
                    foreach ($priors as $label => $prior) {
                        $score = log($prior);
                        foreach ($words as $word) {
                            if (isset($likelihoods[$label][$word])) {
                                $score += log($likelihoods[$label][$word]);
                            } else {
                                $score += log(1 / ($classWordCounts[$label] + $vocabSize));
                            }
                        }
                        $scores[$label] = $score;
                    }

                    // Softmax
                    $expScores = array_map(fn($s) => exp($s), $scores);
                    $sumExp = array_sum($expScores);
                    $probabilities = [];
                    foreach ($expScores as $label => $val) {
                        $probabilities[$label] = $val / $sumExp;
                    }

                    $predictedLabel = array_keys($probabilities, max($probabilities))[0];

                    $allPred[] = $predictedLabel;
                    $allProb[] = $probabilities;
                }

                return back()->with('result', [
                    'sentiments' => $allPred,
                    'probabilities' => $allProb,
                    'texts' => $dataAnalisis
                ]);
            } catch (\Throwable $th) {
                return back()->with('error', 'Gagal memproses file: ' . $th->getMessage());
            }
        }
    }
}
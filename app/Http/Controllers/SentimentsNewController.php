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

    // public function createModel(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:csv,txt'
    //     ]);

    //     if ($request->hasFile('file')) {
    //         try {
    //             $file = $request->file('file');
    //             $raw = array_map('str_getcsv', file($file->getRealPath()));
    //             $header = array_map('trim', $raw[0]);
    //             unset($raw[0]);

    //             // dd($header);

    //             $dataset = [];
    //             foreach ($raw as $row) {
    //                 $row = array_combine($header, $row);
    //                 if (!empty($row['Teks']) && !empty($row['Sentimen'])) {
    //                     $dataset[] = [
    //                         'text' => $row['Teks'],
    //                         'label' => $row['Sentimen']
    //                     ];
    //                 }
    //             }
    //             // dd($dataset);
    //             if (empty($dataset)) {
    //                 return back()->with('error', 'Dataset kosong atau format tidak sesuai.');
    //             }

    //             $results = [];
    //             $documents = [];
    //             $labels = [];
    //             $classes = []; // Menyimpan nama-nama kelas unik (misalnya: positif, negatif, netral)
    //             $classDocCounts = []; // Menyimpan jumlah dokumen per kelas (untuk menghitung probabilitas prior P(class))
    //             $classWordCounts = []; // Menyimpan jumlah kemunculan kata-kata per kelas (untuk menghitung P(word|class))
    //             $vocab = []; // Menyimpan semua kata unik dari seluruh dokumen (vocabulary)
    //             $totalDocs = count($dataset); // Menghitung total jumlah dokumen dalam dataset

    //             foreach ($dataset as $item) {
    //                 $label = $item['label'];
    //                 $words = $this->preprocessing($item['text']);
    //                 $documents[] = $words;
    //                 $labels[] = $item['label'];
    //                 //untuk cek after prepro
    //                 $resultsPre[] = [
    //                     'label' => $label,
    //                     'original_text' => $item['text'],
    //                     'after_preprocessing' => $words
    //                 ];
    //             }
    //             // dd($resultsPre);

    //             $docCount = count($documents);
    //             $df = []; // Document Frequency

    //             // Hitung Document Frequency (DF)
    //             foreach ($documents as $doc) {
    //                 $uniqueWords = array_unique($doc);
    //                 foreach ($uniqueWords as $word) {
    //                     $df[$word] = ($df[$word] ?? 0) + 1;
    //                 }
    //             }
    //             // dd($df);

    //             // Hitung IDF
    //             $idf = [];
    //             foreach ($df as $word => $freq) {
    //                 $idf[$word] = log($docCount / ($freq)); // atau log(1 + $docCount / $freq) untuk smoothing
    //             }
    //             // dd($idf);

    //             // Hitung TF-IDF per dokumen dan akumulasi ke per kelas
    //             foreach ($dataset as $index => $item) {
    //                 $label = $item['label'];
    //                 $doc = $documents[$index];
    //                 $tf = array_count_values($doc);

    //                 $tfidf = [];
    //                 foreach ($tf as $word => $count) {
    //                     $tfidf[$word] = $count * ($idf[$word] ?? 0);
    //                 }

    //                 // Simpan hasil individual
    //                 $results[] = [
    //                     'label' => $label,
    //                     'original_text' => $item['text'],
    //                     'after_preprocessing' => $doc,
    //                     'tfidf' => $tfidf
    //                 ];

    //                 // Akumulasi per kelas
    //                 $classDocCounts[$label] = ($classDocCounts[$label] ?? 0) + 1;

    //                 if (!isset($classes[$label])) {
    //                     $classes[$label] = [];
    //                     $classWordCounts[$label] = 0;
    //                 }

    //                 foreach ($tfidf as $word => $value) {
    //                     $classes[$label][$word] = ($classes[$label][$word] ?? 0) + $value;
    //                     $classWordCounts[$label] += $value;
    //                     $vocab[$word] = true;
    //                 }
    //             }
    //             // dd($results);

    //             // Hitung Prior Prob
    //             $priors = [];
    //             foreach ($classDocCounts as $label => $count) {
    //                 $priors[$label] = $count / $docCount;
    //             }
    //             // dd($priors);
    //             $model = [
    //                 'results' => $results,
    //                 'classes' => $classes,
    //                 'class_word_counts' => $classWordCounts,
    //                 'class_doc_counts' => $classDocCounts,
    //                 'total_docs' => $docCount,
    //                 'priors' => $priors,
    //                 'vocab' => array_keys($vocab),
    //                 'idf' => $idf
    //             ];
    //             // dd($model);

    //             Storage::disk('local')->put('model.json', json_encode($model, JSON_PRETTY_PRINT));

    //             return back()->with('success', 'Model berhasil dilatih dan disimpan!');
    //         } catch (\Throwable $th) {
    //             return back()->with('error', 'Gagal memproses file: ' . $th->getMessage());
    //         }
    //     } else {
    //         return back()->with('error', 'File tidak ditemukan.');
    //     }
    // }

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
            dd($model);
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

    // private function preprocessing($text)
    // {
    //     $text = strtolower($text); // case folding
    //     $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text); // cleaning
    //     $repeatW = preg_replace('/(\w)\1{2,}/', '$1', $cleanText); // Repeat word (banget[tttttt])
    //     $words = preg_split('/\s+/', $repeatW); // tokenisasi

    //     // normalisasi
    //     $normalizedWords = array_map(function ($word) {
    //         return $this->normalizationDictionary[$word] ?? $word;
    //     }, $words);

    //     // stemming
    //     $stemmedWords = array_map(function ($word) {
    //         return $this->stemmer->stem($word);
    //     }, $normalizedWords);

    //     // stopword removal
    //     $filtered = array_diff($stemmedWords, $this->stopwords);
    //     return array_values($filtered);
    // }

    public function preprocessing($text)
    {
        // 1. Lowercase
        $text = strtolower($text);

        // 2. Hilangkan simbol, angka, dan karakter non-huruf
        $text = preg_replace('/[^a-z\s]/', '', $text);

        // 3. Tokenisasi
        $tokens = explode(' ', $text);

        $cleaned = [];
        foreach ($tokens as $word) {
            $word = trim($word);

            // 4. Normalisasi slang
            if (isset($this->normalizationDictionary[$word])) {
                $word = $this->normalizationDictionary[$word];
            }

            // 5. Hilangkan huruf berulang (mantappp → mantap)
            $word = preg_replace('/(\w)\1{2,}/', '$1', $word);

            // 6. Filter panjang kata
            if (strlen($word) < 3 || strlen($word) > 15) continue;

            // 7. Stopword removal
            if (in_array($word, $this->stopwords)) continue;

            // 8. Stemming
            $word = $this->stemmer->stem($word);

            // 9. Final filter
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
        // dd($model);

        $idf = $model['idf'];
        $classes = $model['classes'];
        $priors = $model['priors'];
        $classWordCounts = $model['class_word_counts'];

        // Preprocessing input
        $words = $this->preprocessing($request->input('input_text'));

        // Hitung TF untuk input
        $tf = array_count_values($words);

        // Hitung TF-IDF untuk input
        $tfidfInput = [];
        foreach ($tf as $word => $count) {
            $tfidfInput[$word] = $count * ($idf[$word] ?? 0);
        }

        // Hitung skor probabilitas untuk setiap kelas
        $scores = [];
        foreach ($classes as $label => $wordTfidfs) {
            $score = log($priors[$label]); // log(P(class))
            $totalWords = $classWordCounts[$label];
            $vocabSize = count($model['vocab']);

            foreach ($tfidfInput as $word => $value) {
                $wordValue = $wordTfidfs[$word] ?? 0.0001; // Laplace smoothing
                $score += log($wordValue / ($totalWords + $vocabSize));
            }

            $scores[$label] = $score;
        }

        // Konversi skor log ke probabilitas dengan softmax
        $expScores = array_map(fn($s) => exp($s), $scores);
        $sumExp = array_sum($expScores);
        $probabilities = [];
        foreach ($expScores as $label => $val) {
            $probabilities[$label] = $val / $sumExp;
        }

        // Ambil label tertinggi
        $predictedLabel = array_keys($probabilities, max($probabilities))[0];

        // dd($predictedLabel);

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
            'file' => 'required|file|mimes:csv,txt,xlsx'
        ]);

        if ($request->hasFile('file')) {
            try {
                // Membaca file
                $file = $request->file('file');
                $raw = array_map('str_getcsv', file($file->getRealPath()));
                $header = array_map('trim', $raw[0]);
                unset($raw[0]);
                // dd($raw);
                // AMbil teks dari kolom teks
                $dataAnalisis = [];
                foreach ($raw as $row) {
                    $row = array_combine($header, $row);
                    if(!empty($row['Teks'])){
                        $dataAnalisis[] = $row['Teks'];
                    }
                }
                // dd($dataAnalisis);
                // Ambil model
                $modelJson = Storage::disk('local')->get('model.json');
                $model = json_decode($modelJson, true);
                // dd($model);

                $idf = $model['idf'];
                $classes = $model['classes'];
                $priors = $model['priors'];
                $classWordCounts = $model['class_word_counts'];

                $allPreprocessing = [];
                $allTf = [];
                $allTfIdf = [];
                $allScore = [];
                $allProb = [];
                $allPred = [];
                // Preprocessing input
                foreach ($dataAnalisis as $text) {
                    $words = $this->preprocessing($text);
                    $allPreprocessing[] = $words;
                    // Hitung TF
                    $tf = array_count_values($words);
                    $allTf[] = $tf;
                    // Hitung TF-IDF
                    $tfidfInput = [];
                    foreach ($tf as $word => $count) {
                        $tfidfInput[$word] = $count * ($idf[$word] ?? 0);
                    }
                    $allTfIdf[] = $tfidfInput;
                    // Hitung skor probabilitas untuk setiap kelas
                    $scores = [];
                    foreach ($classes as $label => $wordTfidfs) {
                        $score = log($priors[$label]); // log(P(class))
                        $totalWords = $classWordCounts[$label];
                        $vocabSize = count($model['vocab']);

                        foreach ($tfidfInput as $word => $value) {
                            $wordValue = $wordTfidfs[$word] ?? 0.0001; // Laplace smoothing
                            $score += log($wordValue / ($totalWords + $vocabSize));
                        }

                        $scores[$label] = $score;
                    }
                    $allScore[] = $scores;
                    // Konversi skor log ke probabilitas dengan softmax
                    $expScores = array_map(fn($s) => exp($s), $scores);
                    $sumExp = array_sum($expScores);
                    $probabilities = [];
                    foreach ($expScores as $label => $val) {
                        $probabilities[$label] = $val / $sumExp;
                    }
                    $allProb[] = $probabilities;
                    // Ambil label tertinggi
                    $predictedLabel = array_keys($probabilities, max($probabilities))[0];
                    $allPred[] = $predictedLabel;
                }
                // dd($allPreprocessing);
                // dd($allTf);
                // dd($allTfIdf);
                // dd($allScore);
                // dd($allProb);
                // dd($allPred);
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
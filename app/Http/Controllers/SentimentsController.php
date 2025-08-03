<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TotalSentiment;
use App\Models\Sentiments;
use App\Models\FrequentWords;
use App\Models\AnalysisResult;
use App\Models\Aspect;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use App\Models\ApiInstagram;
use App\Models\InstagramSentiment;
use Rubix\ML\Classifiers\NaiveBayes;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\TfIdfTransformer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\TextNormalizer;
use Rubix\ML\Tokenizers\NGram;
use Rubix\ML\Transformers\StopWordFilter;
use Rubix\ML\Transformers\WordCountVectorizer;
use Illuminate\Support\Facades\Storage;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Spatie\SimpleExcel\SimpleExcelReader;
use Rubix\ML\Transformers\OneHotEncoder;
use Sastrawi\Stemmer\StemmerFactory;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Pipeline;
use Rubix\ML\Transformers\IntervalDiscretizer;
use GuzzleHttp\Client;

// use Phpml\Classification\NaiveBayes;
use Phpml\ModelManager;


class SentimentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sentiment/index');
    }

    public function analyzeSentiment(Request $request)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $text = $request->text;
        //Stemming text
        $stemmerFactory = new StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        $stemmers = $stemmer->stem($text);
        //Memuat model
        $estimator = PersistentModel::load(new Filesystem('model.rbx'));
        //
        $dataset = new Unlabeled(
            [$text]
        );
        $prediction = current($estimator->predict($dataset));
        //Measukan hasil ke database
        Sentiments::create([
            'teks' => $request['text'],
            'sentiment' => $prediction,
        ]);
        return redirect()->route('manual.input')->with(['prediction' => $prediction, 'text' => $text, 'success' => 'Berhasil']);
    }

    public function manualInput()
    {
        return view('sentiment/manual-input');
    }

    public function menguji(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $fileName = time() . '-' .$file->getClientOriginalName();
            
                // Memuat model
                $estimator = PersistentModel::load(new Filesystem('model.rbx'));
                // Menyimpan file
                $filePath = $file->storeAs('public/ManualInput-Predict', $fileName);
                //Memuat File upload
                $loadFile = Storage::path('public/ManualInput-Predict/' .$fileName);
                $data = SimpleExcelReader::create($loadFile)->getRows();

                $dataset = [];
                foreach ($data as $rows) {
                    //Membersihkan text 
                    $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $rows['Teks']);
                    //Stemming text
                    $stemmerFactory = new StemmerFactory();
                    $stemmer  = $stemmerFactory->createStemmer();
                    $stemmers = $stemmer->stem($cleanText);
                    $dataset[] = $stemmers;
                }
                //
                $testing = new Unlabeled($dataset);
                //StopWOrd text
                $stopWords = new StopWordFilter(['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk',
                        'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', ',']);
                $testing->apply($stopWords);
                
                // Membuat prediksi
                $predictions = $estimator->predict($testing);
                
                return redirect()->back()->with(['predictions' => $predictions, 'dataset' => $dataset, 'success' => 'Berhasil']);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan dalam pengujian model.');
            }
        }
    }

    public function instagram()
    {
        $ApiInstagram = ApiInstagram::where('user_id', auth()->user()->id)->get();
        $InstagramSentiment = InstagramSentiment::where('user_id', auth()->user()->id)->get();
        $results = InstagramSentiment::where('user_id', auth()->user()->id)->get();
        return view('sentiment/instagram', compact('ApiInstagram', 'InstagramSentiment', 'results'));
    }
    
    public function predictInstagram()
    {
        try {
            $ApiInstagram = ApiInstagram::where('user_id', auth()->user()->id)->get();
            foreach($ApiInstagram as $ig){
            $filePath = $ig->file;
            }
            
                // Memuat model
                $estimator = PersistentModel::load(new Filesystem('model.rbx'));
                //Memuat File upload
                $loadFile = Storage::path($filePath);
                $data = SimpleExcelReader::create($loadFile)->getRows();

                $dataset = [];
                foreach ($data as $rows) {
                    //Membersihkan text 
                    $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $rows['Teks']);
                    //Stemming text
                    $stemmerFactory = new StemmerFactory();
                    $stemmer  = $stemmerFactory->createStemmer();
                    $stemmers = $stemmer->stem($cleanText);
                    $dataset[] = $stemmers;
                }
                
                //
                $testing = new Unlabeled($dataset);
                //StopWOrd text
                $stopWords = new StopWordFilter(['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk',
                        'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', ',']);
                $testing->apply($stopWords);
                
                // Membuat prediksi
                $predictions = $estimator->predict($testing);
                
                foreach ($predictions as $index => $prediction) {
                    $text = $dataset[$index]; // Teks komentar dari dataset yang sudah diproses
                    $sentiment = $prediction; // Hasil prediksi sentimen
                
                    // Simpan hasil prediksi ke dalam database
                    InstagramSentiment::create([
                        'text' => $text,
                        'sentiment' => $sentiment,
                        'user_id' => Auth::id(),
                    ]);
                }
                return redirect()->back();
                // return redirect()->back()->with(['predictions' => $predictions, 'dataset' => $dataset, 'success' => 'Berhasil']);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan dalam pengujian model.');
            }
    }

    public function shopee() 
    {
        return view('sentiment/shopee');
    }

    public function shopeeMenguji(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $fileName = time() . '-' .$file->getClientOriginalName();
                // Menyimpan file
                $filePath = $file->storeAs('public/ManualInput-Predict', $fileName);
                //Memuat File upload
                $loadFile = Storage::path('public/ManualInput-Predict/' .$fileName);
                $data = SimpleExcelReader::create($loadFile)->getRows();
    
                // Mengambil data dari file Excel
                $data = $data->toArray();

                // Proses data 
                $this->processDataShopee($data);

                // Proses data Sentiment
                // Memuat model
                $estimator = PersistentModel::load(new Filesystem('modelShopee.rbx'));
        
                $dataS = SimpleExcelReader::create($loadFile)->getRows();

                $dataset = [];
                foreach ($dataS as $rows) {
                    //Membersihkan text 
                    $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $rows['Teks']);
                    //Stemming text
                    $stemmerFactory = new StemmerFactory();
                    $stemmer  = $stemmerFactory->createStemmer();
                    $stemmers = $stemmer->stem($cleanText);
                    $dataset[] = $stemmers;
                }
                //
                $testing = new Unlabeled($dataset);
                //StopWOrd text
                $stopWords = new StopWordFilter([
                    'dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk',
                    'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', ',']);
                $testing->apply($stopWords);
                // dd($testing);
                // Membuat prediksi
                $predictions = $estimator->predict($testing);


                //menghitung jumlah positif,negatif,netral
                $totalPositif = count(array_filter($predictions, function($sentiment) {
                    return $sentiment === 'Positif';
                }));

                $totalNetral = count(array_filter($predictions, function($sentiment) {
                    return $sentiment === 'Netral';
                }));

                $totalNegatif = count(array_filter($predictions, function($sentiment) {
                    return $sentiment === 'Negatif';
                }));

                //menyimpan jumlah ke db
                TotalSentiment::updateOrCreate(
                    ['sentiment' => 'Positif'],
                    ['total' => $totalPositif]
                );
                
                TotalSentiment::updateOrCreate(
                    ['sentiment' => 'Negatif'],
                    ['total' => $totalNegatif]
                );
                
                TotalSentiment::updateOrCreate(
                    ['sentiment' => 'Netral'],
                    ['total' => $totalNetral]
                );

                // Definisikan aspek dan kata-kata yang relevan
                $aspects = [
                    'kualitas' => ['kualitas', 'material', 'bahan', 'daya tahan', 'tahan lama', 'awet'],
                    'harga' => ['harga', 'murah', 'mahal', 'diskon', 'nilai'],
                    'pengiriman' => ['pengiriman', 'kirim', 'cepat', 'lambat', 'waktu', 'lama'],
                ];

                // Ekstraksi Aspek dari Komentar
                $aspectData = [];
                foreach ($dataset as $index => $comment) {
                    $words = explode(' ', $comment);
                    foreach ($aspects as $aspect => $keywords) {
                        foreach ($keywords as $keyword) {
                            if (in_array($keyword, $words)) {
                                $aspectData[$aspect][] = [
                                    'comment' => $comment,
                                    'sentiment' => $predictions[$index],
                                ];
                                break;
                            }
                        }
                    }
                }

                // Analisis Sentimen untuk Setiap Aspek
                $aspectSentiments = [];
                foreach ($aspectData as $aspect => $comments) {
                    $positif = 0;
                    $negatif = 0;
                    $netral = 0;

                    foreach ($comments as $data) {
                        if ($data['sentiment'] === 'Positif') {
                            $positif++;
                        } elseif ($data['sentiment'] === 'Negatif') {
                            $negatif++;
                        } elseif ($data['sentiment'] === 'Netral') {
                            $netral++;
                        }
                    }

                    $total = $positif + $negatif + $netral;

                    $aspectSentiments[$aspect] = [
                        'positif' => $positif,
                        'negatif' => $negatif,
                        'netral' => $netral,
                        'total' => $total,
                        'persentasePositif' => $total > 0 ? ($positif / $total) * 100 : 0,
                        'persentaseNegatif' => $total > 0 ? ($negatif / $total) * 100 : 0,
                        'persentaseNetral' => $total > 0 ? ($netral / $total) * 100 : 0,
                    ];

                    // Simpan ke database
                    Aspect::create([
                        'aspect' => $aspect,
                        'positif' => $positif,
                        'negatif' => $negatif,
                        'netral' => $netral,
                        'total' => $total,
                        'persentasePositif' => $aspectSentiments[$aspect]['persentasePositif'],
                        'persentaseNegatif' => $aspectSentiments[$aspect]['persentaseNegatif'],
                        'persentaseNetral' => $aspectSentiments[$aspect]['persentaseNetral'],
                    ]);
                }

                // dd($aspectSentiments);

                // Array kata-kata yang sesuai dengan sentimen
                $positifWords = [
                    'bagus', 'lucu', 'mantap', 'puas', 'nyaman', 'cepat', 'ramah', 'berkualitas', 'tepat',
                    'suka', 'terbaik', 'indah', 'unik', 'memuaskan', 'mulus', 'bagusnya', 'keren', 'mudah',
                    'baik', 'kenyamanan', 'pujian', 'memikat', 'positif', 'gemar', 'mengagumkan', 'sesuai',
                    'suka dengan', 'paling', 'fantastis', 'menyenangkan', 'hebat', 'sangat bagus', 'sempurna',
                    'menghibur', 'luar biasa', 'efisien', 'mudah dipahami', 'tertarik', 'memukau', 'mengagumkan',
                    'memberi', 'memberi kejutan', 'berguna', 'bermanfaat', 'membuat senang', 'menyenangkan',
                    'dipercaya', 'percaya diri', 'menghibur', 'menginspirasi', 'luar biasa', 'membanggakan',
                    'memesan lagi', 'terbantu', 'senang'
                ]; // kata-kata positif
                $negatifWords = [
                    'jelek', 'buruk', 'mengecewakan', 'tidak baik', 'rusak', 'salah', 'lambat', 'tidak ramah', 
                    'bermasalah', 'kurang', 'kecewa', 'terlambat', 'kasar', 'menyebalkan', 'aneh', 'sulit',
                    'mengganggu', 'kesalahan', 'negatif', 'menyusahkan', 'buruknya', 'menjengkelkan', 'malas',
                    'sialan', 'kotor', 'kacau', 'takut', 'masalah', 'mengerikan', 'terganggu', 'membuat marah',
                    'mengejutkan', 'menyebalkan', 'mengecewakan', 'merusak', 'menjengkelkan', 'membuat frustrasi',
                    'tidak nyaman', 'membingungkan', 'menyusahkan', 'menghancurkan', 'menyakitkan', 'menyedihkan',
                    'menyebalkan', 'membuat kecewa', 'menyakitkan', 'mengganggu', 'membuat kesal', 'membosankan',
                    'menyebalkan', 'mengganggu', 'membuat marah'
                ]; // kata-kata negatif
                $netralWords = [
                    'biasa', 'standar', 'netral', 'oke', 'tidak apa-apa', 'umum',
                    'normal', 'senang', 'sederhana', 'langsung', 'cukup', 'kurang lebih',
                    'umumnya', 'begitulah', 'mungkin', 'wajar', 'kadang-kadang',
                    'secara umum', 'lumayan', 'seadanya', 'mungkin saja', 'sekarang', 'pantas', 'nyata',
                    'memenuhi', 'tanggapan', 'penuh', 'cocok', 'bertemu', 'berurusan', 'rekan',
                    'membantu', 'layanan', 'melebihi', 'terlibat', 'kepada', 'mereka', 'memenuhi',
                    'bersih', 'tahan', 'keputusan', 'mendukung', 'rekomendasi', 'memenuhi', 'memenuhi',
                    'keamanan', 'menarik',
                ]; // kata-kata netral

                $frequentPositifWords = [];
                $frequentNegatifWords = [];
                $frequentNetralWords = [];

                // Loop melalui hasil prediksi
                foreach ($predictions as $index => $sentimen) {
                    // Ambil kata-kata dari dataset
                    $currentWords = explode(' ', $dataset[$index]);

                    // Menginisialisasi array kosong untuk kata-kata yang akan diproses sesuai dengan sentimennya
                    $filteredWords = [];

                    // Memproses kata-kata sesuai dengan sentimen
                    if ($sentimen === 'Positif') {
                        $filteredWords = array_intersect($currentWords, $positifWords);
                    } elseif ($sentimen === 'Negatif') {
                        $filteredWords = array_intersect($currentWords, $negatifWords);
                    } elseif ($sentimen === 'Netral') {
                        $filteredWords = array_intersect($currentWords, $netralWords);
                    }

                    // Menambahkan kata-kata yang tersisa ke dalam array yang sesuai dengan sentimen
                    if ($sentimen === 'Positif') {
                        $frequentPositifWords = array_merge($frequentPositifWords, $filteredWords);
                    } elseif ($sentimen === 'Negatif') {
                        $frequentNegatifWords = array_merge($frequentNegatifWords, $filteredWords);
                    } elseif ($sentimen === 'Netral') {
                        $frequentNetralWords = array_merge($frequentNetralWords, $filteredWords);
                    }
                }

                // Menghitung frekuensi kata-kata yang sering muncul
                $sortedPositifWords = array_count_values($frequentPositifWords);
                arsort($sortedPositifWords);
                $top3PositifWords = array_slice($sortedPositifWords, 0, 3);

                $sortedNegatifWords = array_count_values($frequentNegatifWords);
                arsort($sortedNegatifWords);
                $top3NegatifWords = array_slice($sortedNegatifWords, 0, 3);

                $sortedNetralWords = array_count_values($frequentNetralWords);
                arsort($sortedNetralWords);
                $top3NetralWords = array_slice($sortedNetralWords, 0, 3);

                // Menyimpan hanya 3 kata teratas ke dalam database
                foreach ($top3PositifWords as $word => $frequency) {
                    FrequentWords::updateOrCreate(
                        ['sentiment' => 'Positif', 'word' => $word],
                        ['frequency' => $frequency]
                    );
                }

                // Hapus semua kata-kata lama dari database
                FrequentWords::where('sentiment', 'Positif')->delete();
                FrequentWords::where('sentiment', 'Negatif')->delete();
                FrequentWords::where('sentiment', 'Netral')->delete();

                // Masukkan 3 kata teratas untuk masing-masing kategori sentimen
                foreach ($top3PositifWords as $word => $frequency) {
                    FrequentWords::create([
                        'sentiment' => 'Positif',
                        'word' => $word,
                        'frequency' => $frequency
                    ]);
                }

                foreach ($top3NegatifWords as $word => $frequency) {
                    FrequentWords::create([
                        'sentiment' => 'Negatif',
                        'word' => $word,
                        'frequency' => $frequency
                    ]);
                }

                foreach ($top3NetralWords as $word => $frequency) {
                    FrequentWords::create([
                        'sentiment' => 'Netral',
                        'word' => $word,
                        'frequency' => $frequency
                    ]);
                }
                
                //  dd($frequentPositifWords);

                return redirect()->back()->with(['predictions' => $predictions, 'dataset' => $dataset, 'success' => 'Berhasil']);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan dalam proses.');
            }
        }
    }

    private function processDataShopee($data)
    {
        $dataset = [];
        foreach($data as $rows){
            $dataset[] = [
                'text' => $rows['Teks'],
                'sentiment' => $rows['Sentimen'],
            ];
        }

        $samples = [];
        $labels = [];

        foreach ($dataset as $datas){
            $samples[] = $datas['text'];
            $labels[] = $datas['sentiment'];
        }

        $normalizationDictionary = [
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

        $stopwords = ['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk', 'pada', 'oleh', 'dari', 'dalam',
                    'bahwa', 'ini', 'itu', 'adalah', 'yg', 'tapi', 'nya', 'dengan', 'saat', 'lebih', 'tersebut', 'sudah',
                    'belum', 'karena', 'saya', 'kami', 'anda', 'mereka', 'terjadi', 'akan', 'sebagai', 'seperti', 'tersebut',
                    'hingga', 'untuk', 'pada', 'untuk', 'dalam', 'yang', 'itu', 'disini', 'ada', 'sekarang', 'semua', 'maupun',
                    'buat', 'bisa', 'jika', 'setelah', 'kemudian', 'sedang', 'berikut', 'langsung', 'hingga', 'lagi', 'kenapa',
                    'apa', 'begitu', 'siapa', 'tidak', 'kita', 'itu', 'dia', 'kak', 'ya', 'beliau', 'tadi', 'baru', 'terlalu',
                    'harus', 'mau', 'bagi', 'semuanya', 'sama', 'meskipun', 'ketika', 'jadi', 'sehingga', 'lebih', 'untuk',
                    'pasti', 'seperti', 'terus', 'mengapa', 'melalui', 'apalagi', 'hingga', 'adapun', 'apakah', 'padahal', 'bukan'];
        
        // Fungsi normalisasi
        function normalizeText($text, $dictionary) {
            foreach ($dictionary as $key => $value) {
                $text = preg_replace('/\b' . preg_quote($key, '/') . '\b/i', $value, $text); 
            }
            return $text;
        }

        // Fungsi untuk menghapus stopwords
        function removeStopwords($text, $stopwords) {
            foreach ($stopwords as $stopword) {
                $text = preg_replace('/\b' . preg_quote($stopword, '/') . '\b/i', '', $text);
            }
            $text = preg_replace('/\s+/', ' ', $text); // Menghapus spasi berlebih
            return trim($text); // Menghapus spasi ekstra
        }

        $cleanSamples = []; 
        foreach ($samples as $sample)
        {
            $cleanSample = preg_replace('/[^\p{L}\p{N}\s]/u', '', $sample);
            $cleanSample = normalizeText($cleanSample, $normalizationDictionary);
            $stemmerFactory = new StemmerFactory();
            $stemmer  = $stemmerFactory->createStemmer();
            $stemmers = $stemmer->stem($cleanSample);
            $cleanSample = removeStopwords($stemmers, $stopwords);
            $cleanSamples[] = $cleanSample;
        }

        $dataset = new Labeled($cleanSamples, $labels);
        $stopWords = new StopWordFilter(['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk',
            'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', ',', 'yg', 'tapi', 'nya']);
        $dataset->apply($stopWords);

        [$training, $testing] = $dataset->randomize()->split(0.8);

        $estimator = new NaiveBayes([
            'Netral' => 0.333333333334,
            'Positif' => 0.333333333333,
            'Negatif' => 0.333333333333
        ], 2.5);

        $estimator = new Pipeline([
            new TextNormalizer(),
            new WordCountVectorizer(10000, 2, 0.4, new NGram(1, 2)),
            new TfIdfTransformer(),
            new NumericStringConverter(),
            new IntervalDiscretizer(3, true),
        ], $estimator);

        $estimator = new PersistentModel($estimator, new Filesystem('modelShopee.rbx', true));

        $estimator->train($training);

        // Evaluasi model
        $metric = new Accuracy();
        $predictions = $estimator->predict($testing);

        // Ambil label aktual dari file (pastikan kolom 'Sentimen' ada)
        $trueLabels = [];
        foreach ($dataS as $rows) {
            if (isset($rows['Sentimen'])) {
                $trueLabels[] = $rows['Sentimen'];
            }
        }

        // Inisialisasi confusion matrix multi-kelas
        $labelsList = ['Positif', 'Netral', 'Negatif'];
        $confMatrix = [];
        foreach ($labelsList as $actual) {
            foreach ($labelsList as $predicted) {
                $confMatrix[$actual][$predicted] = 0;
            }
        }
        // Hitung confusion matrix
        foreach ($predictions as $i => $predicted) {
            $actual = $trueLabels[$i] ?? null;
            if (in_array($actual, $labelsList) && in_array($predicted, $labelsList)) {
                $confMatrix[$actual][$predicted]++;
            }
        }

        $score = $metric->score($predictions, $testing->labels());

        // Hitung confusion matrix
        $confusionMatrix = $this->confusionMatrix($predictions, $testing->labels());
        
        // Hitung precision, recall, dan F1-Score
        $precision = $this->precision($confusionMatrix);
        $recall = $this->recall($confusionMatrix);
        $f1Score = $this->f1Score($precision, $recall);

        // Simpan hasil evaluasi
        Evaluation::create([
            'accuracy' => $score,
            'precision' => $precision,
            'recall' => $recall,
            'f1_score' => $f1Score,
        ]);

        // dd($score, $precision, $recall, $f1Score);

        // Simpan model jika kinerja memadai (misalnya akurasi lebih dari 80%)
        if ($score > 0.80) {
            $estimator->save();
        } else {
            echo "Model tidak memenuhi standar kinerja.\n";
        }
    }
    

    // Fungsi untuk menghitung confusion matrix
    private function confusionMatrix($predictions, $labels)
    {
        $confusionMatrix = [
            'TP' => 0,  // True Positives
            'TN' => 0,  // True Negatives
            'FP' => 0,  // False Positives
            'FN' => 0   // False Negatives
        ];

        foreach ($predictions as $index => $prediction) {
            $trueLabel = $labels[$index];

            if ($prediction == 'Positif' && $trueLabel == 'Positif') {
                $confusionMatrix['TP']++;
            } elseif ($prediction == 'Negatif' && $trueLabel == 'Negatif') {
                $confusionMatrix['TN']++;
            } elseif ($prediction == 'Positif' && $trueLabel == 'Negatif') {
                $confusionMatrix['FP']++;
            } elseif ($prediction == 'Negatif' && $trueLabel == 'Positif') {
                $confusionMatrix['FN']++;
            }
        }

        return $confusionMatrix;
    }

    // Fungsi untuk menghitung precision
    private function precision($confusionMatrix)
    {
        return $confusionMatrix['TP'] / ($confusionMatrix['TP'] + $confusionMatrix['FP']);
    }

    // Fungsi untuk menghitung recall
    private function recall($confusionMatrix)
    {
        return $confusionMatrix['TP'] / ($confusionMatrix['TP'] + $confusionMatrix['FN']);
    }

    // Fungsi untuk menghitung F1-Score
    private function f1Score($precision, $recall)
    {
        return 2 * (($precision * $recall) / ($precision + $recall));
    }

    public function shopeeMenguji2(Request $request)
    {
        ini_set('memory_limit', '2G');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $fileName = time() . '-' .$file->getClientOriginalName();
                // Menyimpan file
                $filePath = $file->storeAs('public/ManualInput-Predict', $fileName);
                //Memuat File upload
                $loadFile = Storage::path('public/ManualInput-Predict/' .$fileName);
                $data = SimpleExcelReader::create($loadFile)->getRows();
    
                // Mengambil data dari file Excel
                $data = $data->toArray();

                $samples = [];
                $labels = [];
                foreach ($data as $index => $row) {
                    if ($index === 0) continue; // Lewati header jika ada
                    $samples[] = array_slice($row, 0, -1); // Semua kolom kecuali yang terakhir
                    $labels[] = end($row); // Kolom terakhir sebagai label
                }
                // dd($samples);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Terjadi kesalahan dalam proses.');
            }
        }
    }

}
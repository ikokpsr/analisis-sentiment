<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TotalSentiment;
use App\Models\Sentiments;
use App\Models\FrequentWords;
use App\Models\AnalysisResult;
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

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;


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
                $stopWords = new StopWordFilter(['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk',
                        'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', ',']);
                $testing->apply($stopWords);
                
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
                TotalSentiment::create([
                    'sentiment' => 'Positif',
                    'total' => $totalPositif,
                ]);
                TotalSentiment::create([
                    'sentiment' => 'Negatif',
                    'total' => $totalNegatif,
                ]);
                TotalSentiment::create([
                    'sentiment' => 'Netral',
                    'total' => $totalNetral,
                ]);

                // Kata-kata yang sering muncul dalam hasil positif
                $stopWords2 = ['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk', 'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah', 'produk',
                                'lazada', 'saya', 'sangat'];
                $frequentPositifWords = [];
                $frequentNegatifWords = [];
                $frequentNetralWords = [];

                // Loop melalui hasil prediksi
                foreach ($predictions as $index => $sentimen) {
                    // Ambil kata-kata dari dataset
                    $currentWords = explode(' ', $dataset[$index]);

                    // Membersihkan kata-kata yang tidak diinginkan
                    $filteredWords = array_diff($currentWords, $stopWords2);

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
                    FrequentWords::create([
                        'sentiment' => 'Positif',
                        'word' => $word,
                        'frequency' => $frequency,
                    ]);
                }
                foreach ($top3NegatifWords as $word => $frequency) {
                    FrequentWords::create([
                        'sentiment' => 'Negatif',
                        'word' => $word,
                        'frequency' => $frequency,
                    ]);
                }
                foreach ($top3NetralWords as $word => $frequency) {
                    FrequentWords::create([
                        'sentiment' => 'Netral',
                        'word' => $word,
                        'frequency' => $frequency,
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

        $cleanSamples = []; 
        foreach ($samples as $sample)
        {
            $cleanSample = preg_replace('/[^\p{L}\p{N}\s]/u', '', $sample);
            $stemmerFactory = new StemmerFactory();
            $stemmer  = $stemmerFactory->createStemmer();
            $stemmers = $stemmer->stem($cleanSample);
            $cleanSamples[] = $cleanSample;
        }

        $dataset = new Labeled($cleanSamples, $labels);

        $stopWords = new StopWordFilter(['dan', 'atau', 'juga', 'di', 'ke', 'yang', 'dengan', 'untuk',
            'pada', 'oleh', 'dari', 'dalam', 'bahwa', 'ini', 'itu', 'adalah']);
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
        $estimator->save();
    }
}
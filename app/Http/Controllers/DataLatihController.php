<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelReader;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Classifiers\NaiveBayes;
use Rubix\ML\Pipeline;
use Rubix\ML\Transformers\IntervalDiscretizer;
use Rubix\ML\CrossValidation\Reports\AggregateReport;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\CrossValidation\Reports\MulticlassBreakdown;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Kernels\Distance\Minkowski;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\WordCountVectorizer;
use Rubix\ML\Transformers\TextNormalizer;
use Rubix\ML\Transformers\StopWordFilter;
use Rubix\ML\Tokenizers\WordStemmer;
use Rubix\ML\Tokenizers\NGram;
use Rubix\ML\Transformers\TfIdfTransformer;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Illuminate\Support\Facades\Storage;
use Rubix\ML\Tokenizers\Whitespace;
use Sastrawi\Stemmer\StemmerFactory;

class DataLatihController extends Controller
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
        return view('data-latih/index');
    }
    
    public function latih(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $fileName = 'data-latih.xlsx'; // Nama file yang diinginkan
    
                // Menyimpan file di direktori 'uploads' dengan nama yang disesuaikan
                $filePath = $file->storeAs('uploads', $fileName);
    
                // Menggunakan full path file yang disimpan
                $fullPath = public_path($filePath);
            
                return redirect()->back()->with('success', 'File berhasil diunggah dan disimpan.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Terjadi kesalahan dalam mengunggah dan menyimpan file.');
            }
            
        }
        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function melatih()
    {
        try {
            $file = Storage::path('uploads/data-latih.xlsx');

            // Membaca file Excel menggunakan SimpleExcelReader
            $data = SimpleExcelReader::create($file)->getRows();

            // Mengambil data dari file Excel
            $data = $data->toArray();

            // Proses data latih dengan menggunakan metode Naive Bayes
            $this->processData($data);

            return redirect()->back()->with('success', 'Berhasil diproses.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan dalam proses training.');
        }
    }

    private function processData($data)
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
        
        $estimator = new PersistentModel($estimator, new Filesystem('model.rbx', true));

        $estimator->train($training);
        $estimator->save();
        // $estimator->train($dataset);
        // $estimator->save();
    }
}
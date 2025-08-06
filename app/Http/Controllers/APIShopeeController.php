<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class APIShopeeController extends Controller
{
    protected $apiKey;
    protected $apiSecret;
    protected $apiEndpoint;
    protected $shopeId;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->apiKey = '2007237';
        $this->apiSecret = '4c6556756b6f664d4d796d426548586e4c78634e6b6c554b524d5a5873486a56';
        $this->apiEndpoint = 'https://partner.shopeemobile.com/api/v2/shop/get_shop_info';
        $this->shopId = '390065624';
    }
    
    public function testConnection() 
    {
        $accessToken = $this->getAccessToken();
    
        $timestamp = Carbon::now()->timestamp;
        $authorization = $this->generateAuthorization($timestamp, $accessToken);
    
        $response = Http::withHeaders([
            'Authorization' => $authorization,
            'Content-Type' => 'application/json',
        ])->get($this->apiEndpoint, [
            'partner_id' => $this->apiKey,
            'shop_id' => $this->shopId, // Replace with your shop ID
            'timestamp' => $timestamp,
            'access_token' => $accessToken,
            'sign' => $this->generateSign($timestamp),
        ]);
    
        // Process the response
        $data = $response->json();
    
        return response()->json($data);
    }
    
    protected function getAccessToken()
    {
        $timestamp = Carbon::now()->timestamp;
        $sign = $this->generateSign($timestamp);
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://partner.shopeemobile.com/api/v2/auth/token/get', [
            'partner_id' => $this->apiKey,
            'timestamp' => $timestamp,
            'sign' => $sign,
        ]);
    
        $data = $response->json();
    dd($data);
        // Pastikan response memiliki access token
        if (isset($data['access_token'])) {
            return $data['access_token'];
        } else {
            throw new \Exception('Gagal mendapatkan access token. Response: ' . json_encode($data));
        }
    }
    
    protected function generateAuthorization($timestamp, $accessToken)
{
    $dataToSign = "GET|" . $this->apiEndpoint . "|" . $timestamp;
    $signature = hash_hmac('sha256', $dataToSign, $this->apiSecret, true);
    $encodedSignature = base64_encode($signature);

    $authorization = "Bearer " . $this->apiKey . "|" . $timestamp . "|" . $encodedSignature;

    return $authorization;
}

protected function generateSign($timestamp)
{
    $signData = $this->apiKey . '|' . $timestamp;
    $sign = hash_hmac('sha256', $signData, $this->apiSecret, false);

    return $sign;
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        return view('api/shopee-api/index');
    }
    
    
    
    // public function getProductComments($itemId, $shopId)
    // {
    //     $partnerId = "2007237";
    //     $partnerKey = "4c6556756b6f664d4d796d426548586e4c78634e6b6c554b524d5a5873486a56";

    //     $url = 'https://partner.shopeemobile.com/api/v2/item/get_ratings';

    //     $params = [
    //         'partner_id' => $partnerId,
    //         'shopid' => $shopId,
    //         'itemid' => $itemId,
    //         'timestamp' => now()->timestamp,
    //         // Add other required parameters
    //     ];

    //     // Generate signature for authentication
    //     $signature = generateSignature($url, $partnerKey, $params);

    //     $client = new Client();

    //     try {
    //         $response = $client->get($url, [
    //             'query' => $params,
    //             'headers' => [
    //                 'Authorization' => $signature,
    //                 'Content-Type' => 'application/json',
    //             ],
    //         ]);

    //         $data = json_decode($response->getBody(), true);

    //         // Process $data as needed

    //     } catch (\Exception $e) {
    //         // Handle errors
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // private function generateSignature($url, $partnerKey, $params)
    // {
    //     // Implement your signature generation logic here
    //     // Refer to Shopee API documentation for details
    // }
    
    // public function test()
    // {
    //     $partnerId = '2007237';
    //     $partnerKey = '4c6556756b6f664d4d796d426548586e4c78634e6b6c554b524d5a5873486a56';
    //     $shopId = '390065624';

    //     $response = Http::post('https://partner.shopeemobile.com/api/v2/shop/get_shop_info', [
    //         'partner_id' => $partnerId,
    //         'partner_key' => $partnerKey,
    //         'shopid' => $shopId
    //     ]);

    //     $data = $response->json();

    //     dd($response->json());

    //     return $data;
    // }

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

    // public function analisis(Request $request)
    // {
    //     $request->validate([
    //         'input_text' => 'required|string'
    //     ]);

    //     // Ambil model
    //     $modelJson = Storage::disk('local')->get('model.json');
    //     $model = json_decode($modelJson, true);
    //     // dd($model);

    //     $idf = $model['idf'];
    //     $classes = $model['classes'];
    //     $priors = $model['priors'];
    //     $classWordCounts = $model['class_word_counts'];

    //     // Preprocessing input
    //     $words = $this->preprocessing($request->input('input_text'));

    //     // Hitung TF untuk input
    //     $tf = array_count_values($words);

    //     // Hitung TF-IDF untuk input
    //     $tfidfInput = [];
    //     foreach ($tf as $word => $count) {
    //         $tfidfInput[$word] = $count * ($idf[$word] ?? 0);
    //     }

    //     // Hitung skor probabilitas untuk setiap kelas
    //     $scores = [];
    //     foreach ($classes as $label => $wordTfidfs) {
    //         $score = log($priors[$label]); // log(P(class))
    //         $totalWords = $classWordCounts[$label];
    //         $vocabSize = count($model['vocab']);

    //         foreach ($tfidfInput as $word => $value) {
    //             $wordValue = $wordTfidfs[$word] ?? 0.0001; // Laplace smoothing
    //             $score += log($wordValue / ($totalWords + $vocabSize));
    //         }

    //         $scores[$label] = $score;
    //     }

    //     // Konversi skor log ke probabilitas dengan softmax
    //     $expScores = array_map(fn($s) => exp($s), $scores);
    //     $sumExp = array_sum($expScores);
    //     $probabilities = [];
    //     foreach ($expScores as $label => $val) {
    //         $probabilities[$label] = $val / $sumExp;
    //     }

    //     // Ambil label tertinggi
    //     $predictedLabel = array_keys($probabilities, max($probabilities))[0];

    //     // dd($predictedLabel);

    //     return back()->with('result', [
    //         'label' => $predictedLabel,
    //         'probabilities' => $probabilities
    //     ]);
    // }

    // public function analisisFile(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:csv,txt,xlsx'
    //     ]);

    //     if ($request->hasFile('file')) {
    //         try {
    //             // Membaca file
    //             $file = $request->file('file');
    //             $raw = array_map('str_getcsv', file($file->getRealPath()));
    //             $header = array_map('trim', $raw[0]);
    //             unset($raw[0]);
    //             // dd($raw);
    //             // AMbil teks dari kolom teks
    //             $dataAnalisis = [];
    //             foreach ($raw as $row) {
    //                 $row = array_combine($header, $row);
    //                 if(!empty($row['Teks'])){
    //                     $dataAnalisis[] = $row['Teks'];
    //                 }
    //             }
    //             // dd($dataAnalisis);
    //             // Ambil model
    //             $modelJson = Storage::disk('local')->get('model.json');
    //             $model = json_decode($modelJson, true);
    //             // dd($model);

    //             $idf = $model['idf'];
    //             $classes = $model['classes'];
    //             $priors = $model['priors'];
    //             $classWordCounts = $model['class_word_counts'];

    //             $allPreprocessing = [];
    //             $allTf = [];
    //             $allTfIdf = [];
    //             $allScore = [];
    //             $allProb = [];
    //             $allPred = [];
    //             // Preprocessing input
    //             foreach ($dataAnalisis as $text) {
    //                 $words = $this->preprocessing($text);
    //                 $allPreprocessing[] = $words;
    //                 // Hitung TF
    //                 $tf = array_count_values($words);
    //                 $allTf[] = $tf;
    //                 // Hitung TF-IDF
    //                 $tfidfInput = [];
    //                 foreach ($tf as $word => $count) {
    //                     $tfidfInput[$word] = $count * ($idf[$word] ?? 0);
    //                 }
    //                 $allTfIdf[] = $tfidfInput;
    //                 // Hitung skor probabilitas untuk setiap kelas
    //                 $scores = [];
    //                 foreach ($classes as $label => $wordTfidfs) {
    //                     $score = log($priors[$label]); // log(P(class))
    //                     $totalWords = $classWordCounts[$label];
    //                     $vocabSize = count($model['vocab']);

    //                     foreach ($tfidfInput as $word => $value) {
    //                         $wordValue = $wordTfidfs[$word] ?? 0.0001; // Laplace smoothing
    //                         $score += log($wordValue / ($totalWords + $vocabSize));
    //                     }

    //                     $scores[$label] = $score;
    //                 }
    //                 $allScore[] = $scores;
    //                 // Konversi skor log ke probabilitas dengan softmax
    //                 $expScores = array_map(fn($s) => exp($s), $scores);
    //                 $sumExp = array_sum($expScores);
    //                 $probabilities = [];
    //                 foreach ($expScores as $label => $val) {
    //                     $probabilities[$label] = $val / $sumExp;
    //                 }
    //                 $allProb[] = $probabilities;
    //                 // Ambil label tertinggi
    //                 $predictedLabel = array_keys($probabilities, max($probabilities))[0];
    //                 $allPred[] = $predictedLabel;
    //             }
    //             // dd($allPreprocessing);
    //             // dd($allTf);
    //             // dd($allTfIdf);
    //             // dd($allScore);
    //             // dd($allProb);
    //             // dd($allPred);
    //             return back()->with('result', [
    //                 'sentiments' => $allPred,
    //                 'probabilities' => $allProb,
    //                 'texts' => $dataAnalisis
    //             ]);
    //         } catch (\Throwable $th) {
    //             return back()->with('error', 'Gagal memproses file: ' . $th->getMessage());
    //         }
    //     }
    // }
}
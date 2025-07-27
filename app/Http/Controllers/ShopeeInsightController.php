<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FrequentWords;
use App\Models\TotalSentiment;
use App\Models\Aspect;

class ShopeeInsightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $words = FrequentWords::all();
        $aspects = Aspect::all();
        
        $positifTotal = TotalSentiment::where('sentiment', 'Positif')->value('total');
        $netralTotal = TotalSentiment::where('sentiment', 'Netral')->value('total');
        $negatifTotal = TotalSentiment::where('sentiment', 'Negatif')->value('total');

        $total = $positifTotal + $negatifTotal + $netralTotal;

        if ($total > 0) {
            $persentasePositif = ($positifTotal / $total) * 100;
            $persentaseNetral = ($netralTotal / $total) * 100;
            $persentaseNegatif = ($negatifTotal / $total) * 100;
        } else {
            // Jika tidak ada data, tetapkan persentase ke 0 atau lakukan tindakan lain sesuai kebutuhan
            $persentasePositif = 0;
            $persentaseNetral = 0;
            $persentaseNegatif = 0;
        }

        $roundPositif = round($persentasePositif, 2);
        $roundNetral = round($persentaseNetral, 2);
        $roundNegatif = round($persentaseNegatif, 2);

        $wordPositif = frequentWords::where('sentiment', 'Positif')->pluck('word')->toArray();
        $wordNetral = frequentWords::where('sentiment', 'Netral')->pluck('word')->toArray();
        $wordNegatif = frequentWords::where('sentiment', 'negatif')->pluck('word')->toArray();

        $wordsAll = [
            'positif' => join(', ', $wordPositif),
            'negatif' => join(', ', $wordNegatif),
            'netral' => join(', ', $wordNetral),
        ];

        $frequentWords = FrequentWords::select('sentiment', 'word', 'frequency')
        ->orderByDesc('frequency')
        ->get()
        ->groupBy('sentiment');

        // Ambil top 3 per sentimen
        $topWords = [
            'Positif' => $frequentWords['Positif']->take(3),
            'Netral' => $frequentWords['Netral']->take(3),
            'Negatif' => $frequentWords['Negatif']->take(3),
        ];

        return view('insight/shopee/index', compact('words', 'aspects', 'wordsAll', 'roundPositif', 'roundNetral', 'roundNegatif', 'wordPositif','topWords'));
    }

    public function getPieChartData()
    {
        $positifTotal = TotalSentiment::where('sentiment', 'Positif')->value('total');
        $netralTotal = TotalSentiment::where('sentiment', 'Netral')->value('total');
        $negatifTotal = TotalSentiment::where('sentiment', 'Negatif')->value('total');

        return compact('positifTotal', 'netralTotal', 'negatifTotal');
    }
}
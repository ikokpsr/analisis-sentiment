<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FrequentWords;
use App\Models\TotalSentiment;

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

        return view('insight/shopee/index', compact('words'));
    }

    public function getPieChartData()
    {
        $positifTotal = TotalSentiment::where('sentiment', 'Positif')->value('total');
        $netralTotal = TotalSentiment::where('sentiment', 'Netral')->value('total');
        $negatifTotal = TotalSentiment::where('sentiment', 'Negatif')->value('total');

        return compact('positifTotal', 'netralTotal', 'negatifTotal');
    }
}
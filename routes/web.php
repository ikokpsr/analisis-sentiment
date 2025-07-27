<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIInstagramController;
use App\Http\Controllers\APIShopeeController;
use App\Http\Controllers\SentimentsController;
use App\Http\Controllers\DataLatihController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopeeInsightController;
use App\Http\Controllers\OpenAIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/data-latih', [DataLatihController::class, 'index'])->name('data-latih.index');
Route::post('/data-latih/latih', [DataLatihController::class, 'latih'])->name('data-latih.latih');
Route::get('/data-latih/melatih', [DataLatihController::class, 'melatih'])->name('melatih.data');
// Analisis Sentimen
// Route::get('/sentiment', [SentimentsController::class, 'index'])->name('sentiment.index');
Route::post('/sentiment/analyze', [SentimentsController::class, 'analyzeSentiment'])->name('sentiment.analyze');
Route::get ('/sentiment/manual-input', [SentimentsController::class, 'manualInput'])->name('manual.input');
Route::post('/sentiment/uji', [SentimentsController::class, 'menguji'])->name('menguji.data');
//Instagram API
Route::resource('/api/instagram-api', APIInstagramController::class);
Route::get('/api/instagram-api', [APIInstagramController::class, 'index'])->name('instagram-api.index');
//Instagram 
Route::post('/getcommenttexts', [APIInstagramController::class, 'getCommentText'])->name('get.comment');
Route::get('/auth', [APIInstagramController::class, 'auth'])->name('auth');
Route::get('/sentiment/instagram', [SentimentsController::class, 'instagram'])->name('instagram.sentiment');
Route::post('/sentiment/predict-instagram', [SentimentsController::class, 'predictInstagram'])->name('predict.instagram');
//Shopee API
Route::resource('/api/shopee-api', APIShopeeController::class);
Route::get('/api/shopee-api', [APIShopeeController::class, 'index'])->name('shopee-api.index');
//Shopee
Route::get('/sentiment/shopee', [SentimentsController::class, 'shopee'])->name('shopee.sentiment');
Route::post('/sentiment/ujiShopee', [SentimentsController::class, 'shopeeMenguji'])->name('shopeeMenguji.data');
Route::get('open-ai', [OpenAIController::class, 'index']);
//Shopee Insight
Route::resource('/insight/shopee', ShopeeInsightController::class);
Route::get('/insight/shopee', [ShopeeInsightController::class, 'index'])->name('shopee-insight.index');
Route::get('/api/pie-chart-data', [ShopeeInsightController::class, 'getPieChartData']);

Route::get('/test', [APIShopeeController::class, 'test'])->name('test');
Route::get('/shopee/test-connection', [APIShopeeController::class, 'testConnection']);
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
}
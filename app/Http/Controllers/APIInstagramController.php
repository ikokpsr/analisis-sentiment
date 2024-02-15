<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApiInstagram;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;
use Illuminate\Support\Facades\Http;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Storage;

class APIInstagramController extends Controller
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
        $ApiInstagram = ApiInstagram::where('user_id', Auth::id())->get();
        
        // dd($ApiInstagram);
        if ($ApiInstagram->isEmpty()) {
            return view('api/instagram-api/index', compact('ApiInstagram'));
        } else {
            foreach($ApiInstagram as $ig){
                $filePath = $ig->file;
                }
            $loadFile = Storage::path($filePath);
            $data = SimpleExcelReader::create($loadFile)->getRows();
                $dataComments = [];
                foreach ($data as $rows) {
                    $dataComments[] = $rows['Teks'];
                }
            return view('api/instagram-api/index', compact('ApiInstagram', 'dataComments'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('api/instagram-api/create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'app_id' => 'required|string',
            'app_secret' => 'required|string',
            'page_id' => 'required|string',
        ]);
    
        ApiInstagram::create([
            'app_id' => $request['app_id'],
            'app_secret' => $request['app_secret'],
            'page_id' => $request['page_id'],
            'status' => '2',
            'user_id' => Auth::id(),
        ]);
        
        return redirect()->route('instagram-api.index')->with('success','Berhasil!');
    }

    public function getCommentText(Request $request)
    {
        $redirectUri = 'https://akubelajar.xyz/auth/';
        $request->validate([
            'access_token' => 'required|string',
        ]);
        
        
        $getId = ApiInstagram::where('user_id', Auth::id())->first();
        if ($getId) {
            $appId = $getId->app_id;
            $appSecret = $getId->app_secret;
        } else {
            // Tangani jika app_id tidak ditemukan
            echo 'tidak ditemukan';
            exit;
        }
        
        $fb = new Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v17.0',
        ]);
    
        try {
            $helper = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken($redirectUri);
            // Panggil metode auth dan kirimkan accessToken
            $accessToken = $request->access_token;
            return $this->auth($accessToken);
        } catch (FacebookResponseException $e) {
            // Tangani kesalahan respons dari Facebook
            dd($e->getMessage());
        } catch (FacebookSDKException $e) {
            // Tangani kesalahan SDK Facebook
            dd($e->getMessage());
        }
    
        if (!isset($accessToken)) {
            // Jika access token tidak tersedia, arahkan pengguna ke halaman autentikasi Facebook
            $loginUrl = $helper->getLoginUrl($redirectUri, ['email', 'public_profile']);
            return redirect()->away($loginUrl);
        }
        
        
    }
    
    public function auth($accessToken)
    {
        $ApiInstagram = ApiInstagram::where('user_id', auth()->user()->id)->get();
        $getId = ApiInstagram::where('user_id', Auth::id())->first();
        if ($getId) {
            $pageId = $getId->page_id;
        } else {
            // Tangani jika app_id tidak ditemukan
            echo 'tidak ditemukan';
            exit;
        }
    
        $url = "https://graph.facebook.com/v17.0/{$pageId}?fields=instagram_business_account&access_token={$accessToken}";
    
        $response = Http::get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            $instagramBusinessAccount = $data['instagram_business_account'];
            $instagramBusinessAccountId = $instagramBusinessAccount['id'];
            $this->proccessGetIDPost($instagramBusinessAccountId, $accessToken);
        } else {
            // Tangani kesalahan jika permintaan gagal
            $error = $response->json();
            dd($error);
        }
        
        foreach($ApiInstagram as $ig){
        $filePath = $ig->file;
        }
        $loadFile = Storage::path($filePath);
        $data = SimpleExcelReader::create($loadFile)->getRows();
            $dataComments = [];
            foreach ($data as $rows) {
                $dataComments[] = $rows['Teks'];
            }
        
        return view('api/instagram-api/index', compact('ApiInstagram', 'dataComments'));
        
    }

    private function proccessGetIDPost($instagramBusinessAccountId, $accessToken)
    {
    
        $url = "https://graph.facebook.com/v17.0/{$instagramBusinessAccountId}/media?access_token={$accessToken}";
    
        $response = Http::get($url);
    
        if ($response->successful()) {
            $data = $response->json();
            $posts = $data['data'];
    
            $postIds = [];
    
            foreach ($posts as $post) {
                $postId = $post['id'];
                $postIds[] = $postId;
            }
        } else {
            // Tangani kesalahan jika permintaan gagal
            $error = $response->json();
            dd($error);
        }
    
        $allComments = []; // Array untuk menyimpan semua komentar
    
        foreach ($postIds as $postId) {
            $url = "https://graph.facebook.com/v17.0/{$postId}?fields=comments&access_token={$accessToken}";
    
            $response = Http::get($url);
    
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['comments']) && is_array($data['comments'])) {
                    $comments = $data['comments']['data'];
    
                    // Jika tidak ada komentar, lewati iterasi
                    if (empty($comments)) {
                        continue;
                    }
    
                    $allComments = array_merge($allComments, $comments); // Gabungkan komentar ke array allComments
                } else {
                    // Tidak ada komentar yang tersedia untuk posting ini
                    continue;
                }
            } else {
                // Tangani kesalahan jika permintaan gagal
                $error = $response->json();
                dd($error);
            }
        }
    
        $commentTexts = [];

        foreach ($allComments as $comment) {
            $commentTexts[] = $comment['text'];
        }
        
        $fileName = 'comments_' . now()->format('YmdHis') . '_' . Auth::id() . '.xlsx';
        
        $excelWriter = SimpleExcelWriter::create(storage_path("app/public/comments/{$fileName}"));
        
        $excelWriter->addHeader(['Teks']);

        // Tambahkan setiap teks komentar ke file Excel sebagai baris baru
        foreach ($commentTexts as $commentText) {
            $excelWriter->addRow([$commentText]);
        }
        
        ApiInstagram::where('user_id', auth()->user()->id)->update([
                'status' => '1',
                'file' => 'public/comments/' . $fileName,
            ]);
        
        
        // dd($commentTexts);
    }
}
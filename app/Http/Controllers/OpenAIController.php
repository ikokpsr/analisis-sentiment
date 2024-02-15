<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIController extends Controller
{
     /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): JsonResponse
    {
        try {
            $response = OpenAI::createCompletion([
                'model' => 'text-davinci-003',
                'prompt' => 'Contoh teks untuk GPT',
                'max_tokens' => 50,
            ]);

            $generatedText = $response->choices[0]->text;

            return response()->json(['generated_text' => $generatedText]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan dalam berkomunikasi dengan ChatGPT.']);
        }
    }
}
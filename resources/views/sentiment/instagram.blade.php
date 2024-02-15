@extends('layouts.app')

@section('title', 'File Predict')

@section('content')

<div class="flex bg-gray-100">
    @include('components.sidebar')
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
                <!--<table class="min-w-full leading-normal">-->
                <!--    <thead>-->
                <!--        <tr>-->
                <!--            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Teks</th>-->
                <!--            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sentimen</th>-->
                <!--        </tr>-->
                <!--    </thead>-->
                <!--    <tbody>-->
                <!--            <tr>-->
                <!--                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">abjkdb</td>-->
                <!--                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">abdjasj</td>-->
                <!--            </tr>-->
                <!--    </tbody>-->
                <!--</table>-->
                @if($ApiInstagram->count() > 0 && $InstagramSentiment->count() < 1)
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Analysis Sentiment Data Comment Instagram
                    </label>
                    
                    <p class="flex justify-center text-xl">Silakan klik tombol dibawah untuk lakukan analisis.</p>
                    <a href="{{ route('predict.instagram') }}" class="flex justify-center mt-4">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mb-4">Predict!</button>
                    </a>
                </div>
                @elseif($ApiInstagram->count() < 1)
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Analysis Sentiment Data Comment Instagram
                    </label>
                    <p class="flex justify-center text-x">Silakan lakukan pengambilan data terlebih dahulu. 
                        <a href="/api/instagram-api" class="text-blue-600">klik disini</a>
                    </p>
                </div>
                @elseif($InstagramSentiment->count() > 0)
                 <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Result Table
                    </label>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Text</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sentiment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $index => $result)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ ++$index }}</td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->text }}</td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $result->sentiment }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
        </main>
    </div>
</div>
@endsection
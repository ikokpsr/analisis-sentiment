@extends('layouts.app')

@section('title', 'File Predict')

@section('content')

<div class="flex bg-gray-100">
    @include('components.sidebar')
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 md:mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Input Text
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Sentiment Analysis</h2>
                    <form action="{{ route('sentiment.analyze') }}" method="POST">
                        @csrf
                        <textarea class="resize-none border rounded w-full h-24 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline " name="text" id="text"></textarea>
                        {{-- @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif --}}
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold mt-2 py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Predict
                        </button>
                    </form>
                </div>
                <!-- ... -->
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Input File
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Sentiment Analysis</h2>
                    <form method="POST" action="{{ route('shopeeMenguji.data') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <input type="file" name="file" id="file2" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @if(session('error'))
                                <div class="alert alert-success">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>  
            <div class="grid grid-cols-1">  
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Result
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Sentiment Analysis</h2>
                    <span>
                        @if(session('prediction'))
                            <div>
                                @if(session('text'))
                                    <span class="font-bold" > text :</span>
                                    <span> {{ session('text') }}</span>
                                @endif
                            </div>
                            @if(session('prediction') == 'Positif')
                            <div class="mt-4">
                                <span class="font-bold">Sentiment :</span>
                                <span class="box-content w-12 px-2 border-4 border-green-200 rounded-md bg-green-200 text-green-800">
                                    <i class="fas fa-thumbs-up"></i>
                                    {{ session('prediction') }}
                                </span>
                            </div>
                            @elseif (session('prediction') == 'Negatif')
                            <div class="mt-4">
                                <span class="font-bold">Sentimen :</span>
                                <span class="box-content w-12 px-2 border-4 border-red-200 rounded-md bg-red-200 text-red-800">
                                    <i class="fas fa-thumbs-up"></i>
                                    {{ session('prediction') }}
                                </span>
                            </div>
                            @else
                            <div class="mt-4">
                                <span class="font-bold">Sentimen :</span>
                                <span class="box-content w-12 px-2 border-4 border-gray-200 rounded-md bg-gray-200 text-gray-800">
                                    <i class="fas fa-thumbs-up"></i>
                                    {{ session('prediction') }}
                                </span>
                            </div>
                            @endif
                        @endif
                        @if (session('success') && session('dataset'))
                            <div class="max-h-96 overflow-y-scroll">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Teks</th>
                                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sentimen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('predictions') as $index => $prediction)
                                            <tr>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ session('dataset')[$index] }}</td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $prediction }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </span>
                </div>
            </div> 
        </main>
    </div>
</div>
@endsection
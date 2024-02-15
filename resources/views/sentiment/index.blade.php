@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="flex h-screen bg-gray-100">
    @include('components.sidebar')
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2" for="file2">
                        Input Text
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Analisis Sentimen</h2>
                    <form action="{{ route('sentiment.analyze') }}" method="POST">
                        @csrf
                        <textarea class="resize-none border rounded w-full h-24 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline " name="text" id="text"></textarea>
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold mt-2 py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Predict
                        </button>
                    </form>
                </div>
                <!-- ... -->
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2" for="file2">
                        Input File
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Analisis Sentimen</h2>
                    <form method="POST" action="{{ route('data-latih.latih') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <input type="file" name="file" id="file2" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @if(session('error'))
                            <div class="text-red-500 text-sm">{{ session('error') }}</div>
                            @endif
                            @if(session('success'))
                            <div class="text-green-500 text-sm">{{ session('success') }}</div>
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
                    <label class="block text-gray-400 text-sm font-bold mb-2" for="file2">
                        Result
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Analisis Sentimen</h2>
                </div>
            </div>       
        </main>
    </div>
</div>

@endsection
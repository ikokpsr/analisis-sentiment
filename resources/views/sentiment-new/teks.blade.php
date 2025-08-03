@extends('layouts.app')

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
                    <form action="{{ route('analisis.analyze') }}" method="POST">
                        @csrf
                        <textarea class="resize-none border rounded w-full h-24 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline " name="input_text" id="text"></textarea>
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
                        Result
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Sentiment Analysis</h2>
                     @if (session('result'))
                        <div class="mt-5">
                            <h3 class="font-semibold">Hasil Sentimen: <span class="text-blue-600">{{ ucfirst(session('result.label')) }}</span></h3>
                            <p class="mt-2"><strong>Probabilitas:</strong></p>
                            <ul>
                                @foreach (session('result.probabilities') as $label => $score)
                                    <li>{{ ucfirst($label) }}: {{ number_format($score, 4) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
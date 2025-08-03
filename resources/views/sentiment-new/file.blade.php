@extends('layouts.app')

@section('title', 'Create Model')

@section('content')

<div class="flex">
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 md:mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Input File
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Upload Data Ulasan </h2>
                    <form method="POST" action="{{ route('analisis.file') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <input type="file" name="file" id="file" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('file')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
                <!-- ... -->
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Result
                    </label>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Sentiment Analysis</h2>
                    <span>
                        @if(session('error'))
                            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-2">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-2">
                                {{ session('success') }}
                            </div>
                        @endif
                       @if (session('result'))
                            <div class="overflow-x-auto mt-4">
                                <table class="min-w-full table-auto border border-collapse border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-200">
                                            <th class="px-4 py-2 border">No</th>
                                            <th class="px-4 py-2 border">Teks Asli</th>
                                            <th class="px-4 py-2 border">Sentimen</th>
                                            <th class="px-4 py-2 border">Probabilitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('result.sentiments') as $index => $label)
                                            <tr>
                                                <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                                <td class="px-4 py-2 border">
                                                    {{ session('result.texts')[$index] ?? '-' }}
                                                </td>
                                                <td class="px-4 py-2 border font-semibold">
                                                    @if($label === 'Positif')
                                                        <span class="text-green-600">Positif</span>
                                                    @elseif($label === 'Negatif')
                                                        <span class="text-red-600">Negatif</span>
                                                    @else
                                                        <span class="text-yellow-600">Netral</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 border text-sm">
                                                    @php
                                                        $probs = session('result.probabilities')[$index];
                                                    @endphp
                                                    <ul class="list-disc list-inside">
                                                        @foreach($probs as $cls => $val)
                                                            <li>{{ ucfirst($cls) }}: {{ number_format($val * 100, 2) }}%</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
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
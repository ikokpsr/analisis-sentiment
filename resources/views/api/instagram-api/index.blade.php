@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="flex h-screen bg-gray-100">
    @include('components.sidebar')
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
             <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 md:mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Application Detail
                    </label>
                    
                    @if($ApiInstagram->count() > 0)
                    @foreach ($ApiInstagram as $Apii)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="font-medium " for="app_id">App ID :</label>
                                <input type="text" id="app_id" value="{{ $Apii->app_id }}" class="border rounded p-2 outline-none focus:shadow-outline w-72" disabled>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="font-medium " for="app_secret">App Secret :</label>
                                <input type="password" id="app_secret" value="{{ $Apii->app_secret }}" class="border rounded p-2 outline-none focus:shadow-outline w-72" disabled>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="font-medium " for="page_id">Page ID :</label>
                                <input type="text" id="page_id" value="{{ $Apii->page_id }}" class="border rounded p-2 outline-none focus:shadow-outline w-72" disabled>
                            </div>
                        </div>
                    @endforeach
                    @elseif($ApiInstagram->count() < 1)
                        <a href="{{ route('instagram-api.create') }}">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mb-4">input</button>
                        </a>
                        <!-- Tidak ada data untuk ditampilkan -->
                        <p class"text-red-300">Tidak ada data API Instagram yang tersedia, silahkan input data terlebih dahulu</p>
                    @endif
                </div>
                <!-- ... -->
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Input Acces Token 
                    </label>
                    @if($ApiInstagram->count() < 1)
                        <p class"text-red-300">Tidak ada data API Instagram yang tersedia, silahkan input data terlebih dahulu</p>
                    <!--<h2 class="text-xl font-bold text-gray-800 mb-4">Sentiment Analysis</h2>-->
                    @elseif($ApiInstagram->count() > 0)
                        @foreach ($ApiInstagram as $Apii2)
                            @if($Apii2->status == '2')
                                <form method="POST" action="{{ route('get.comment') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <input type="text" id="access_token" name="access_token" class="border rounded p-2 outline-none focus:shadow-outline w-72" required>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                Get Comment Texts
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <p class"text-blue-600">Anda sudah melakukan pengambilan data komentar.</p>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>  
            @foreach ($ApiInstagram as $Apii3)
                @if($Apii3->status == '1')
                    <div class="grid grid-cols-1">  
                        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                            <label class="block text-gray-400 text-sm font-bold mb-2">
                                Comments Data
                            </label>
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Teks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataComments as $index => $comment)
                                        <tr>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ ++$index }}</td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $comment }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        </main>
</div>
@endsection
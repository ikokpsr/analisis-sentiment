@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="flex h-screen bg-gray-100">
    @include('components.sidebar')
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
            <div class="w-full max-w-md mx-auto">
                <form method="POST" action="{{ route('data-latih.latih') }}" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                  @csrf
                  <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                      Pilih File Excel
                    </label>
                    <input type="file" name="file" id="file" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('file')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                         </span>
                    @enderror
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                  </div>
                  <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"">
                      Unggah
                    </button>
                    @if(session('success'))
                        <a href="{{ route('melatih.data') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"">
                            latih
                        </a>
                    @endif
                  </div>
                </form>
              </div>
              
        </main>
    </div>
</div>

@endsection
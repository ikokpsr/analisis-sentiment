@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div class="flex h-screen bg-gray-100">
    @include('components.sidebar')
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
            <form action="{{ route('instagram-api.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2">
                    <div class="col-span-2 mx-auto space-y-4 bg-white p-4">
                        <div class="flex justify-between">
                            <label class="font-medium " for="app_id">App ID :</label>
                            <input type="text" id="app_id" name="app_id" class="border rounded p-2 outline-none focus:shadow-outline w-72" required>
                        </div>
                        <div class="flex-justify-between">
                            <label class="font-medium " for="api_secret">App Secret :</label>
                            <input type="password" id="app_secret" name="app_secret" class="border rounded p-2 outline-none focus:shadow-outline w-72" required>
                        </div>
                        <div class="flex justify-between">
                            <label class="font-medium " for="page_id">Page ID :</label>
                            <input type="text" id="page_id" name="page_id" class="border rounded p-2 outline-none focus:shadow-outline w-72" required>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
              </form>
        </main>
</div>
@endsection
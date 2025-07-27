@extends('layouts.app')

@section('title', 'Home')

@section('content')

<div >
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <div class="text-center text-4xl md:text-5xl lg:text-6xl xl:text-7xl">
            <span class="inline-flex p-4 font-bold">
                <span class="text-blue-500 uppercase">Insight</span>
                <span class="text-purple-500 uppercase">ify</span>
            </span>
        </div>
        <div class="bg-white bg-opacity-70 shadow-md rounded-md p-10 mx-auto my-6 max-w-2xl">
            <p class="prose lg:prose-xl text-gray-700 text-lg mb-8 text-justify">
                Website ini dibuat untuk memberikan wawasan mendalam tentang persepsi pelanggan terhadap suatu merek melalui analisis sentimen komentar produk menggunakan algoritma Naive Bayes. Dengan pendekatan ini, bertujuan untuk memberikan pemahaman yang akurat dan efisien terhadap sentimen pelanggan, yang selanjutnya dapat menjadi landasan untuk pengambilan keputusan bisnis yang lebih baik.
            </p>
            <!--<p class="text-gray-500 text-center text-sm">-->
            <!--    Terimakasih! -->
            <!--</p>-->
        </div>





        
        {{-- <div class="bg-gray-100 p-4 rounded-lg">
            <div class="mb-4">
                <div class="text-sm">Sentimen</div>
                <div class="flex items-center mt-2">
                    <div class="w-1/3">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 60%"></div>
                    </div>
                    <div class="w-1/3">
                        <div class="bg-red-500 h-2 rounded-full" style="width: 20%"></div>
                    </div>
                    <div class="w-1/3">
                        <div class="bg-gray-500 h-2 rounded-full" style="width: 20%"></div>
                    </div>
                </div>
                <div class="flex justify-between mt-2">
                    <div class="text-xs text-gray-500">Positif: 1000 teks</div>
                    <div class="text-xs text-gray-500">Negatif: 500 teks</div>
                    <div class="text-xs text-gray-500">Netral: 500 teks</div>
                </div>
            </div>
         </div> --}}
         {{-- <div class="grid grid-cols-2 gap-4">
            <div class="col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">
                    Application Detail
                </label>
                <canvas class="p-10" id="chartBar"></canvas>
            </div>
         </div>
         
         
        </main>
        <!-- Chart bar -->
<script>
    const labelsBarChart = [
      "Positif",
      "Netral",
      "Negatif",
    ];
    const dataBarChart = {
      labels: labelsBarChart,
      datasets: [
        {
          label: "My First dataset",
          backgroundColor: "hsl(252, 82.9%, 67.8%)",
          borderColor: "hsl(252, 82.9%, 67.8%)",
          data: [0, 10, 5],
        },
      ],
    };
  
    const configBarChart = {
      type: "bar",
      data: dataBarChart,
      options: {},
    };
  
    var chartBar = new Chart(
      document.getElementById("chartBar"),
      configBarChart
    );
  </script>
</div> --}}

{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    <form action="{{ route('logout') }}" method="POST" class="group">
                        @csrf
                        <a class="block px-4 py-2 mt-2 text-sm font-semibold text-red-400 bg-transparent rounded-lg md:mt-0 hover:text-white focus:text-gray-900 hover:bg-red-400 focus:bg-gray-200 focus:outline-none focus:shadow-outline">
                          <button type="submit" class="w-full">Logout</button>
                        </a>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection

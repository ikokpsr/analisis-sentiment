@extends('layouts.app')

@section('title', 'Shopee Insight')

@section('content')

<div class="flex">
    <div class="flex flex-col flex-1">
        @include('components.navbar')
        <main class="flex-1 p-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 md:mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Pie Chart Detail
                    </label>
                    <canvas id="pieChartSentiment" width="600" height="400"></canvas>
                </div>
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                       Bar Chart Words Frequency
                    </label>
                    <canvas id="barChartWords" width="600" height="400"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 md:mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Stacked Bar Chart Aspect Analysis
                    </label>
                    <canvas id="stackedBarAspek" width="600" height="400"></canvas>
                </div>
                <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Words Frequency Detail
                    </label>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Sentiment</th>
                                    <th class="py-2 px-4 border-b">Word</th>
                                    <th class="py-2 px-4 border-b">Frequency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($words as $word)
                                    <tr>
                                        <td class="py-2 px-4 border-b text-center">{{ $word->sentiment }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $word->word }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $word->frequency }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">
                    Aspect Analysis Detail
                </label>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Aspect</th>
                                <th class="py-2 px-4 border-b">Positive</th>
                                <th class="py-2 px-4 border-b">Negative</th>
                                <th class="py-2 px-4 border-b">Neutral</th>
                                <th class="py-2 px-4 border-b">Total</th>
                                <th class="py-2 px-4 border-b">Positive %</th>
                                <th class="py-2 px-4 border-b">Negative %</th>
                                <th class="py-2 px-4 border-b">Neutral %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aspects as $aspect)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->aspect }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->positif }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->negatif }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->netral }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->total }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->persentasePositif }}%</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->persentaseNegatif }}%</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $aspect->persentaseNetral }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">
                    Conclusion
                </label>
                <p class="prose lg:prose-xl mb-2 text-justify">
                    Data dari pie chart menunjukan bahwa hasil dari sentimen Positif, Negatif, dan Netral dalam persentase sebagai berikut :
                </p>
                <p class="prose lg:prose-xl mb-2 text-justify">- Sentimen Posistif menghasilkan persentase sebanyak {{ $roundPositif }}% dengan kata yang sering muncul yaitu 
                    {{ $wordsAll['positif'] }}.
                </p>
                <p class="prose lg:prose-xl mb-2 text-justify">- Sentimen Negatif menghasilkan persentase sebanyak {{ $roundNegatif }}% dengan kata yang sering muncul yaitu
                    {{ $wordsAll['negatif'] }}.
                </p>
                <p class="prose lg:prose-xl mb-2 text-justify">- Sentimen Netral menghasilkan persentase sebanyak {{ $roundNetral }}% dengan kata yang sering muncul yaitu
                    {{ $wordsAll['netral'] }}.
                </p>
                <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                    Data dari analisis aspek menunjukkan bahwa pandangan pelanggan terhadap aspek harga, kualitas, dan pengiriman dalam persentase sebagai berikut:
                </p>
                @foreach ($aspects as $aspect)
                    <p class="prose lg:prose-xl mb-2 text-justify">- Aspek {{ $aspect->aspect }} memiliki sentimen positif sebanyak {{ $aspect->persentasePositif }}%, sentimen negatif sebanyak {{ $aspect->persentaseNegatif }}%, dan sentimen netral sebanyak {{ $aspect->persentaseNetral }}%.</p>
                @endforeach
                @if ($roundPositif > $roundNegatif && $roundPositif > $roundNetral)
                    <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                        Wawasan Utama :
                    </p>
                    {{-- <p class="prose lg:prose-xl mb-2 text-justify">Sebagian besar pelanggan memiliki pandangan positif terhadap merek ini. Kata-kata seperti {{ $wordsAll['positif'] }} sering muncul, menunjukkan kekuatan utama merek ini dalam kualitas dan keandalan.</p> --}}
                    <p class="prose lg:prose-xl mb-2 text-justify">Sebagian besar pelanggan memiliki pandangan positif terhadap merek ini. Kata-kata seperti {{ $wordsAll['positif'] }} sering muncul, menunjukkan bahwa kualitas produk dan kesesuaiannya dengan ekspektasi pelanggan menjadi keunggulan utama. Namun, aspek pengiriman memiliki keluhan tertinggi, sehingga memerlukan perhatian lebih untuk meningkatkan kepuasan pelanggan.</p>
                    <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                        Rekomendasi :
                    </p>
                    <p class="prose lg:prose-xl mb-2 text-justify">1. Mempertahankan dan Meningkatkan Kualitas Produk</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Pastikan produk sesuai dengan deskripsi dan foto yang ditampilkan di platform penjualan.</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Gunakan bahan berkualitas tinggi untuk menjaga daya tahan produk.</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Terus pantau umpan balik pelanggan guna melakukan perbaikan yang diperlukan.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">2. Mengoptimalkan Strategi Pemasaran Berbasis Sentimen Positif</p>   
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Manfaatkan media sosial dan testimoni pelanggan dengan kata-kata seperti {{ $wordsAll['positif'] }} untuk memperkuat citra merek.</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Tawarkan program loyalitas atau insentif bagi pelanggan yang memberikan ulasan positif.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">3. Meningkatkan Kualitas Pengiriman untuk Mengurangi Keluhan</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Bekerja sama dengan jasa ekspedisi yang lebih andal atau memberikan opsi pengiriman premium.</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Gunakan kemasan yang lebih aman untuk mengurangi risiko kerusakan produk selama pengiriman.</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Berikan estimasi waktu pengiriman yang lebih akurat dan transparan kepada pelanggan.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">4. Memanfaatkan Keunggulan Harga sebagai Daya Saing</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Dengan mayoritas pelanggan menilai harga secara positif (69.41%), pertahankan harga yang kompetitif tanpa mengorbankan kualitas.</p>
                        <p class="prose lg:prose-xl mb-2 ml-4 text-justify">- Berikan promo menarik seperti bundling produk atau diskon loyalitas untuk meningkatkan daya tarik pembelian.</p>
                    <p class="prose lg:prose-xl mb-2 mt-2 text-justify"> Secara keseluruhan, merek ini memiliki citra yang positif di mata pelanggan, terutama dalam hal kualitas produk dan harga. Namun, aspek pengiriman masih menjadi perhatian utama karena memiliki persentase sentimen negatif yang cukup tinggi. Dengan menerapkan strategi perbaikan ini, kepuasan pelanggan dapat semakin ditingkatkan, sehingga berdampak positif pada reputasi dan penjualan merek.</p>
                       
                @elseif ($roundNegatif > $roundPositif && $roundNegatif > $roundNetral)
                    <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                        Wawasan :
                    </p>
                    <p class="prose lg:prose-xl mb-2 text-justify">Sebagian besar pelanggan memiliki pandangan negatif terhadap merek ini. Kata-kata seperti {{ $wordsAll['negatif'] }} sering muncul, menunjukkan bahwa ada masalah yang signifikan dengan harga dan kecepatan layanan.</p>
                    <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                        Rekomendasi :
                    </p>
                    <p class="prose lg:prose-xl mb-2 text-justify">1. Segera tinjau dan perbaiki area yang paling banyak dikeluhkan, seperti harga dan layanan pelanggan.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">2. Tingkatkan komunikasi dengan pelanggan untuk memahami lebih baik keluhan mereka.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">3. Implementasikan program loyalitas atau diskon untuk meningkatkan kepuasan pelanggan.</p>

                @elseif ($roundNetral > $roundPositif && $roundNetral > $roundNegatif)
                    <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                        Wawasan :
                    </p>
                    <p class="prose lg:prose-xl mb-2 text-justify">Pandangan pelanggan terhadap merek ini cukup beragam. Banyak yang merasa netral, dengan kata-kata seperti {{ $wordsAll['netral'] }} sering muncul.</p>
                    <p class="prose lg:prose-xl mb-2 mt-4 text-justify">
                        Rekomendasi :
                    </p>
                    <p class="prose lg:prose-xl mb-2 text-justify">1. Identifikasi faktor-faktor yang menyebabkan pandangan netral dan cari cara untuk meningkatkannya menjadi pandangan positif.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">2. Perbaiki area yang mendapat umpan balik negatif.</p>
                    <p class="prose lg:prose-xl mb-2 text-justify">3. Gunakan strategi pemasaran yang lebih fokus untuk menonjolkan kekuatan merek dan meningkatkan persepsi positif.</p>
                @endif
            </div>
        </main>
    </div>
</div>
<script>
    var pieChartCanvas = document.getElementById("pieChartSentiment");
    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    // Function to fetch data from the server
    function fetchData() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/pie-chart-data', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                updatePieChart(response);
            }
        };
        xhr.send();
    }

    // Function to update pie chart with new data
    function updatePieChart(data) {
        var pieChart = new Chart(pieChartCanvas, {
            type: 'pie',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [data.positifTotal, data.netralTotal, data.negatifTotal],
                    backgroundColor: ['#57e187', '#d1d5db', '#ef4444']
                }]
            },
            options: {
                plugins: {
                    labels: {
                        render: 'percentage',
                        fontColor: ['black', 'black', 'black'],
                        precision: 2,
                        fontStyle: 'bold'
                    }
                }
            }
        });
    }
    // Call the fetchData function to initially populate the chart
    fetchData();
</script>

<script>
    const ctx = document.getElementById('barChartWords').getContext('2d');
    const barChartWords = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['bagus', 'sesuai', 'suka', 'kurang', 'rusak', 'kecewa', 'cukup', 'lumayan', 'oke'],
            datasets: [
                {
                    label: 'Positif',
                    data: [799, 498, 336, 0, 0, 0, 0, 0, 0],
                    backgroundColor: '#57e187',
                    barThickness: 30, 
                    maxBarThickness: 40
                },
                {
                    label: 'Negatif',
                    data: [0, 0, 0, 578, 105, 82, 0, 0, 0],
                    backgroundColor: '#ef4444',
                    barThickness: 30,
                    maxBarThickness: 40
                },
                {
                    label: 'Netral',
                    data: [0, 0, 0, 0, 0, 0, 221, 146, 130],
                    backgroundColor: '#d1d5db',
                    barThickness: 30,
                    maxBarThickness: 40
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Frekuensi Kata Tertinggi per Sentimen'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                labels: {
                    render: function (args) {
                        if (args.value === 0) return ''; // hide zero values
                        return args.value;
                    },
                    fontColor: '#000',
                    fontStyle: 'bold',
                    precision: 0,
                    fontSize: 14,
                    position: 'outside',
                    textMargin: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Frekuensi'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Kata'
                    },
                    ticks: {
                        autoSkip: false
                    }
                }
            }
        }
    });
</script>

<script>
    const ctxAspek = document.getElementById('stackedBarAspek').getContext('2d');
    const stackedBarAspek = new Chart(ctxAspek, {
        type: 'bar',
        data: {
            labels: ['Kualitas', 'Harga', 'Pengiriman'],
            datasets: [
                {
                    label: 'Positif (%)',
                    data: [49.85, 69.41, 54.49],
                    backgroundColor: '#57e187'
                },
                {
                    label: 'Negatif (%)',
                    data: [32.88, 19.26, 37.15],
                    backgroundColor: '#ef4444'
                },
                {
                    label: 'Netral (%)',
                    data: [17.27, 11.33, 8.36],
                    backgroundColor: '#d1d5db'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Sentimen Berdasarkan Aspek (%)'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toFixed(2) + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Aspek'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Persentase (%)'
                    }
                }
            }
        }
    });
</script>

@endsection
        
@extends('layouts.app')

@section('title', 'Shopee Insight')

@section('content')

<div class="flex h-screen bg-gray-100">
    @include('components.sidebar')
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
                        Words Frequency Detail
                    </label>
                    <div class="container mx-auto my-5">
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
                {{-- <div class="col-span-2 md:col-span-1 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">
                        Bar Chart Detail
                    </label>
                    <canvas id="barChartSentiment" width="600" height="400"></canvas>
                </div> --}}
            </div>
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2">
                    Implementasi
                </label>
                <p class="flex justify-center text-x">Silakan lakukan pengambilan data terlebih dahulu. 
                    <a href="/api/instagram-api" class="text-blue-600">klik disini</a>
                </p>
            </div>
        </main>
    </div>
</div>
{{-- Pie Chart --}}
{{-- <script>
    var pieChartCanvas = document.getElementById("pieChartSentiment");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 18;

var pieData = {
    labels: [
        "Positif",
        "Netral",
        "Negatif",
    ],
    datasets: [
        {
            data: [133.3, 86.2, 52.2],
            backgroundColor: [
                "#57e187",
                "#d1d5db",
                "#ef4444",
            ]
        }]
};

var pieChart = new Chart(pieChartCanvas, {
  type: 'pie',
  data: pieData
});
</script> --}}
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
                labels: ['Positif', 'Netral', 'Negatif'],
                datasets: [{
                    data: [data.positifTotal, data.netralTotal, data.negatifTotal],
                    backgroundColor: ['#57e187', '#d1d5db', '#ef4444']
                }]
            }
        });
    }

    // Call the fetchData function to initially populate the chart
    fetchData();
</script>

<script>
    var barChartCanvas = document.getElementById("barChartSentiment");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var barData = {
        labels: [
            "Positif",
            "Netral",
            "Negatif",
        ],
        datasets: [
            {
            data: [133.3, 86.2, 52.2],
            backgroundColor: [
                "#57e187",
                "#d1d5db",
                "#ef4444",
            ]
        }]
    };
var barChart = new Chart(barChartCanvas, {
  type: 'bar',
  data: barData
});
</script>
{{-- Bar Chart --}}
{{-- <script>
    var labels = ["Positif", "Netral", "Negatif"];
        var data = [133.3, 86.2, 52.2];
        var backgroundColors = ["#57e187", "#d1d5db", "#ef4444"];

        // Mengambil elemen canvas
        var ctx = document.getElementById('barChartSentiment').getContext('2d');

        // Membuat bar chart
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Analisis Sentiment',
                    data: data,
                    backgroundColor: backgroundColors
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Analisis Sentiment'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
</script> --}}
@endsection
        
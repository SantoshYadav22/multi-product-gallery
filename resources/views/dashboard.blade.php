<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} Stats
        </h2>

        @section('content')
            <div class="max-w-4xl mx-auto py-6">

                <div class="bg-white p-6 rounded-lg shadow">
                    <canvas id="statsChart" height="100"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('statsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Products', 'Carts', 'Users'],
                        datasets: [{
                            label: 'Total Count',
                            data: [{{ $productCount }}, {{ $cartCount }}, {{ $userCount }}],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.6)',
                                'rgba(34, 197, 94, 0.6)',
                                'rgba(234, 179, 8, 0.6)'
                            ],
                            borderColor: [
                                'rgba(59, 130, 246, 1)',
                                'rgba(34, 197, 94, 1)',
                                'rgba(234, 179, 8, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            </script>
        @endsection

    </x-slot>


</x-app-layout>

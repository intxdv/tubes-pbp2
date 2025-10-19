@extends('admin.layout')

@section('title', 'Statistik Penjualan')

@section('content')
<h1 class="page-title">Statistik Penjualan</h1>

<div class="card-container" id="statistics-summary">
    <div class="card">
        <h3>Total Omset Penjualan</h3>
        <p id="stat-total-revenue">...</p>
    </div>
    <div class="card">
        <h3>Kuantiti Produk Terjual</h3>
        <p id="stat-total-quantity">...</p>
    </div>
    <div class="card">
        <h3>Presentase Pesanan Sukses</h3>
        <p id="stat-success-rate">...</p>
    </div>
    <div class="card">
        <h3>Nilai Rata-Rata Penilaian Produk</h3>
        <p id="stat-avg-rating">...</p>
    </div>
</div>

<div class="charts-container">
    <div class="chart-box">
        <h4>Grafik Total Omset Penjualan</h4>
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-box">
        <h4>Total Omset Berdasarkan Kategori Produk</h4>
        <canvas id="categoryChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
let revenueChart = null;
let categoryChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Load summary statistics
    fetch('/admin/api/statistics/summary')
        .then(response => response.json())
        .then(stats => {
            document.getElementById('stat-total-revenue').textContent = 'Rp ' + stats.totalRevenue.toLocaleString('id-ID');
            document.getElementById('stat-total-quantity').textContent = stats.totalQuantity + ' pcs';
            document.getElementById('stat-success-rate').textContent = stats.successRate + '%';
            document.getElementById('stat-avg-rating').textContent = stats.averageRating;
        });

    // Initialize Revenue Chart
    initRevenueChart();
    
    // Initialize Category Chart
    initCategoryChart();
});

function initRevenueChart() {
    fetch('/admin/api/statistics/revenue-chart')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            if (revenueChart) {
                revenueChart.destroy();
            }
            
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Omset (Rp)',
                        data: data.data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.2)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
}

function initCategoryChart() {
    fetch('/admin/api/statistics/category-chart')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            if (categoryChart) {
                categoryChart.destroy();
            }

            // Add percentages to labels
            const labels = data.labels.map((label, index) => {
                return `${label} (${data.percentages[index]}%)`;
            });
            
            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: [
                            '#2563eb', '#7c3aed', '#db2777',
                            '#dc2626', '#ea580c', '#65a30d',
                            '#0891b2', '#4f46e5', '#be123c'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    plugins: {
                        legend: {
                            position: 'right',
                            align: 'center',
                            labels: {
                                padding: 20,
                                boxWidth: 12,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const percentage = data.percentages[context.dataIndex];
                                    return `Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
}
</script>
@endpush
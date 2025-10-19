@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<h1 class="page-title">Dashboard</h1>

<!-- Informasi Admin -->
<div class="section" style="margin-bottom: 30px;">
    <h2 class="section-title">Informasi Admin</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <div>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Nama</p>
            <p style="font-weight: 600; font-size: 16px;">{{ $admin->name }}</p>
        </div>
        <div>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Email</p>
            <p style="font-weight: 600; font-size: 16px;">{{ $admin->email }}</p>
        </div>
        <div>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Role</p>
            <p style="font-weight: 600; font-size: 16px;">Administrator</p>
        </div>
        <div>
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Total Akun Pembeli</p>
            <p style="font-weight: 600; font-size: 16px;">{{ number_format($totalBuyers) }}</p>
        </div>
    </div>
</div>

<!-- Ringkasan Pesanan -->
<div class="card-container">
    <div class="card">
        <h3>Total Pesanan</h3>
        <p>{{ number_format($totalOrders) }}</p>
    </div>
    <div class="card">
        <h3>Pesanan Selesai</h3>
        <p>{{ number_format($completedOrders) }}</p>
    </div>
    <div class="card">
        <h3>Pesanan Pending</h3>
        <p>{{ number_format($pendingOrders) }}</p>
    </div>
</div>

<!-- Laporan Pendapatan -->
<div class="card-container">
    <div class="card">
        <h3>Pendapatan Mingguan</h3>
        <p>Rp {{ number_format($weekly) }}</p>
    </div>
    <div class="card">
        <h3>Pendapatan Bulanan</h3>
        <p>Rp {{ number_format($monthly) }}</p>
    </div>
    <div class="card">
        <h3>Pendapatan Tahunan</h3>
        <p>Rp {{ number_format($yearly) }}</p>
    </div>
</div>

<!-- Grafik Pendapatan -->
<div class="section">
    <h2 class="section-title">Grafik Pendapatan Bulanan</h2>
    <div class="chart-container">
        <canvas id="revenueChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format data for chart
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    const monthlyData = @json($monthlyRevenue);
    const chartData = Array(12).fill(0);
    
    monthlyData.forEach(item => {
        chartData[item.month - 1] = item.total;
    });

    // Initialize chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartData,
                backgroundColor: '#2c3e50',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
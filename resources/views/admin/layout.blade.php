<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Market X Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #2c3e50;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-header h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-menu a {
            display: block;
            padding: 15px 20px;
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background-color: #e9ecef;
        }

        .sidebar-menu a.active {
            background-color: #e9ecef;
            border-left: 4px solid #2c3e50;
            padding-left: 16px;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .btn-logout {
            width: 100%;
            padding: 12px;
            background-color: #ffffff;
            color: #dc2626;
            border: 1px solid #dc2626;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #b91c1c;
            color: #ffffff;
        }
        
        /* Confirmation Modal */
        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .confirm-modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        /* CARDS */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 25px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .card h3 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .card p {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
        }

        /* SECTION */
        .section {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        /* TABLE */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            padding: 10px 20px;
            background-color: #2c3e50;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1a252f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f8f9fa;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-action {
            padding: 6px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background-color: #3b82f6;
            color: #ffffff;
        }

        .btn-edit:hover {
            background-color: #2563eb;
        }

        .btn-delete {
            background-color: #dc2626;
            color: #ffffff;
        }

        .btn-delete:hover {
            background-color: #b91c1c;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        /* CHARTS */
        .chart-container {
            position: relative;
            height: 350px;
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-box {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 30px;
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chart-box h4 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .chart-box canvas {
            flex: 1;
            max-height: 320px;
        }

        /* ALERTS */
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #ecfdf5;
            border-color: #34d399;
            color: #065f46;
        }

        .alert-error {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
        }

        /* Modal Styles */
        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .confirm-modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }

        .confirm-modal-content h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .confirm-modal-content p {
            margin-bottom: 20px;
            color: #4b5563;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        /* Button Styles */
        .btn-secondary {
            padding: 8px 16px;
            background-color: #e5e7eb;
            color: #374151;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-danger {
            padding: 8px 16px;
            background-color: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>MarketX</h2>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products') ? 'active' : '' }}">Produk</a>
            <a href="{{ route('admin.transactions') }}" class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">Transaksi</a>
        </div>
        <div class="sidebar-footer">
            <button onclick="showLogoutConfirmation()" class="btn-logout">Logout</button>
            
            <!-- Logout Confirmation Modal -->
            <div id="logoutConfirmModal" class="confirm-modal">
                <div class="confirm-modal-content">
                    <h3>Konfirmasi Logout</h3>
                    <p>Apakah Anda yakin ingin keluar?</p>
                    <div class="modal-actions">
                        <button onclick="hideLogoutConfirmation()" class="btn-secondary">
                            Batal
                        </button>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-danger">
                                Ya, Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success" id="success-alert">
                {{ session('success') }}
                <button type="button" class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" id="error-alert">
                {{ session('error') }}
                <button type="button" class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        @endif

        @yield('content')

        <!-- Global notification -->
        <div id="notification" class="alert" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 1000;">
            <span id="notification-message"></span>
            <button type="button" class="close-alert" onclick="hideNotification()">&times;</button>
        </div>
    </div>

    <script>
        function showLogoutConfirmation() {
            document.getElementById('logoutConfirmModal').style.display = 'flex';
        }
        
        function hideLogoutConfirmation() {
            document.getElementById('logoutConfirmModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('logoutConfirmModal');
            if (event.target === modal) {
                hideLogoutConfirmation();
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
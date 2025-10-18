<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketX - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* small utilities for active nav */
        .hidden { display: none; }
        .tab-active { border-bottom: 2px solid #3b82f6; color:#3b82f6; }
        .nav-link-active { background-color: #eff6ff; color:#2563eb; font-weight:600; }
        html, body { height:100%; margin:0; }
    </style>
</head>
<body class="bg-gray-100">
    @yield('content')
</body>
</html>

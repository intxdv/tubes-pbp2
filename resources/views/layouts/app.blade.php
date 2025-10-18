<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Marketplace</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        html, body { height: 100%; margin:0; padding:0; }
        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        nav { background: #333; color: #fff; padding: 10px 20px; }
        nav a { color: #fff; margin-right: 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        main {
            flex: 1;
            padding: 20px;
        }
        footer {
            background: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            margin-top: 0;
        }
        .product-card { border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin-bottom: 15px; display: flex; align-items: center; }
        .product-card img { width: 100px; height: 100px; object-fit: cover; margin-right: 15px; border-radius: 5px; }
        .product-info h3 { margin:0; font-size: 1.2em; }
        .product-info p { margin: 5px 0 0 0; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav style="background:#2563eb; color:white; padding:0; position:sticky; top:0; z-index:100; box-shadow:0 2px 8px #0001;">
        <div style="max-width:1100px; margin:auto; display:flex; justify-content:space-between; align-items:center; height:64px;">
            <a href="/" style="font-size:1.5rem; font-weight:700; letter-spacing:1px;">MarketX</a>
            <div style="display:flex; align-items:center; gap:24px;">
                @guest
                    <a href="/register" style="color:white; font-weight:500; text-decoration:none; margin-right:12px;">Register</a>
                    <a href="/login" style="color:white; font-weight:500; text-decoration:none;">Login</a>
                @else
                    <a href="/cart" style="margin-right:18px; display:inline-block; vertical-align:middle; position:relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="white"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007.6 17h8.8a1 1 0 00.95-.68L21 13M7 13V6h13"/></svg>
                        @php
                            // Use session-backed cart count. Orders are only created at final checkout.
                            $cart = session('cart.items', []);
                            $cartCount = is_array($cart) ? count($cart) : 0;
                        @endphp
                        @if($cartCount > 0)
                            <span id="nav-cart-count" style="position:absolute; top:-6px; right:-6px; background:#ef4444; color:white; border-radius:50%; font-size:13px; padding:2px 7px; min-width:22px; text-align:center;">{{ $cartCount }}</span>
                        @else
                            <span id="nav-cart-count" style="display:none; position:absolute; top:-6px; right:-6px; background:#ef4444; color:white; border-radius:50%; font-size:13px; padding:2px 7px; min-width:22px; text-align:center;"></span>
                        @endif
                    </a>
                    <a href="/dashboard" style="display:inline-flex; align-items:center; gap:8px; margin-right:12px; color:white; text-decoration:none; font-weight:500;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#e5e7eb" viewBox="0 0 24 24" style="vertical-align:middle;"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-3.314 3.134-6 7-6s7 2.686 7 6"/></svg>
                        @php $showUsername = \Illuminate\Support\Facades\Schema::hasColumn('users', 'username') && auth()->user()->username; @endphp
                        <span>{{ $showUsername ? auth()->user()->username : auth()->user()->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:transparent; color:white; border:none; font-weight:500; cursor:pointer;">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Konten halaman -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        &copy; {{ date('Y') }} Marketplace
    </footer>
</body>
</html>

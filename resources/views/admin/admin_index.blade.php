@extends('layouts.dashboard')
@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800">MarketX</h2>
        </div>
        <div class="px-4 pt-3">
            <!-- Back to homepage -->
            <div class="mb-4">
                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-blue-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
            </div>
        </div>
        <div class="p-4 flex flex-col items-center">
            @php $avatar = $user->avatar ?? 'images/profile.png'; @endphp
            <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset($avatar) }}" class="w-20 h-20 rounded-full object-cover mb-3">
            <div class="font-semibold">{{ $user->username }}</div>
            <div class="text-sm text-gray-500">{{ $user->email }}</div>
        </div>
        <nav class="mt-4">
            <div id="main-nav">
                <a href="#" class="nav-link block py-2.5 px-6 transition duration-200 nav-link-active" onclick="showContent('dashboard-admin', this)">
                    Dashboard
                </a>
                <a href="#" class="nav-link block py-2.5 px-6 transition duration-200" onclick="showContent('orders', this)">
                    Produk
                </a>
                <a href="#" class="nav-link block py-2.5 px-6 transition duration-200" onclick="showContent('orders', this)">
                    Transaksi
                </a>
                <a href="#" class="nav-link block py-2.5 px-6 transition duration-200" onclick="showContent('orders', this)">
                    Statistik
                </a>
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                    @csrf
                    <button type="submit" class="nav-link block text-left w-full py-2.5 px-6 transition duration-200">Logout</button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Sections will be toggled via JS -->
        <div>
            @if(View::hasSection('dashboard-content'))
                @yield('dashboard-content')
            @else
                @include('admin.admin_dashboard')
                @include('admin.categories')
            @endif
        </div>
    </main>
</div>

<!-- include the JS used to toggle tabs/sections -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contentSections = document.querySelectorAll('.content-section');
        const navLinks = document.querySelectorAll('#main-nav .nav-link');
    const subNavContainer = null;

        window.showContent = function(id, element, isSubLink = false) {
            contentSections.forEach(section => section.classList.add('hidden'));
            if (id === 'dashboard-admin') {
                document.getElementById('dashboard-admin').classList.remove('hidden');
            } else {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            }

            navLinks.forEach(link => link.classList.remove('nav-link-active'));
            element.classList.add('nav-link-active');
        }

        // Initialize default section
        const defaultLink = document.querySelector('a[onclick*="dashboard-admin"]');
        if (defaultLink) {
            showContent('dashboard-admin', defaultLink);
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('content')
    @if(Auth::check())
        {{-- Kalau sudah login langsung redirect --}}
        <script>window.location = "{{ route('dashboard') }}";</script>
    @else
        @if ($errors->any())
            <div style="color:red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:32px; min-height:100vh; align-items:center; justify-content:center; background:#f3f4f6;">
            <div style="grid-column:3/5; max-width:370px; width:100%; background:white; border-radius:16px; box-shadow:0 4px 16px #0002; padding:32px; border:1px solid #f3f4f6; margin:auto;">
              <h2 style="text-align:center; font-size:1.5rem; font-weight:700; color:#2563eb; margin-bottom:24px;">Login</h2>
              <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div style="margin-bottom:16px;">
                  <label for="email" style="font-weight:500;">Email</label>
                  <input type="email" name="email" id="email" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
                </div>
                <div style="margin-bottom:16px;">
                  <label for="password" style="font-weight:500;">Password</label>
                  <input type="password" name="password" id="password" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;">
                </div>
                <button type="submit" style="width:100%; background:#2563eb; color:white; padding:10px; border-radius:8px; font-weight:600; border:none;">Login</button>
              </form>
              <p style="margin-top:24px; text-align:center; font-size:1rem; color:#555;">
                Belum Punya Akun? 
                <a href="{{ route('register') }}" style="font-weight:600; color:#2563eb; text-decoration:none;">Daftar</a>
              </p>
            </div>
        </div>
    @endif
@endsection

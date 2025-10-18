@extends('layouts.app')

@section('content')
<div style="display:grid; grid-template-columns:repeat(6,1fr); gap:32px; min-height:100vh; align-items:center; justify-content:center; background:#f3f4f6;">
    <div style="grid-column:3/5; max-width:520px; width:100%; background:white; border-radius:16px; box-shadow:0 4px 16px #0002; padding:32px; border:1px solid #f3f4f6; margin:auto;">
        <h2 style="text-align:center; font-size:1.5rem; font-weight:700; color:#2563eb; margin-bottom:24px;">Registrasi</h2>
        @if ($errors->any())
            <div style="color:red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <input type="text" name="name" placeholder="Nama Lengkap" required style="padding:10px; border-radius:8px; border:1px solid #ccc;">
                <input type="text" name="username" placeholder="Username" required style="padding:10px; border-radius:8px; border:1px solid #ccc;">
                <input type="email" name="email" placeholder="Email" required style="grid-column:1 / 3; padding:10px; border-radius:8px; border:1px solid #ccc;">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px;">
                <input type="password" name="password" placeholder="Password" required style="padding:10px; border-radius:8px; border:1px solid #ccc;">
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required style="padding:10px; border-radius:8px; border:1px solid #ccc;">
            </div>
            <div style="margin-top:12px; display:flex; gap:12px;">
                <input type="text" name="no_hp" placeholder="No. HP (opsional)" style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;">
                <input type="text" name="alamat" placeholder="Alamat (opsional)" style="flex:1; padding:10px; border-radius:8px; border:1px solid #ccc;">
            </div>
            <button type="submit" style="width:100%; margin-top:16px; background:#2563eb; color:white; padding:10px; border-radius:8px; font-weight:600; border:none;">Daftar</button>
        </form>
    </div>
</div>
@endsection

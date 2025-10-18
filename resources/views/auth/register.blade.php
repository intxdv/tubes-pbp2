@extends('layouts.app')

@section('content')
    <h1>Register</h1>
    @if($errors->any())
        <div style="color:#ef4444; margin-bottom:12px;">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="/register" style="max-width:400px; margin:auto; display:flex; flex-direction:column; gap:12px;">
        @csrf
        <label>Nama:</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Konfirmasi Password:</label>
        <input type="password" name="password_confirmation" required>
        <button type="submit" style="background:#2563eb; color:white; border:none; padding:8px 20px; border-radius:6px; font-weight:500; cursor:pointer;">Register</button>
    </form>
@endsection

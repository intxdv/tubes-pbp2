@extends('layouts.app')

@section('content')
    <h2>Manajemen Kategori</h2>
    <a href="/admin/dashboard" style="display:inline-block; margin-bottom:18px; background:#e5e7eb; color:#222; border:none; padding:8px 20px; border-radius:6px; font-weight:500; text-decoration:none;">Back</a>
    <form method="POST" action="/admin/categories" style="max-width:400px; margin-bottom:32px;">
        @csrf
        <div style="display:flex; gap:12px; align-items:center;">
            <input type="text" name="name" placeholder="Nama kategori baru" class="form-control" required style="flex:1;">
            <button type="submit" style="background:#2563eb; color:white; border:none; padding:8px 18px; border-radius:6px; font-weight:500;">+ Tambah</button>
        </div>
    </form>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td>
                        <form method="POST" action="/admin/categories/{{ $cat->id }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:#ef4444; color:white; border:none; padding:4px 12px; border-radius:4px;">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

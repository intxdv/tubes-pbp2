@extends('dashboard.index')
@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold">Alamat Pengiriman</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:underline">Kembali ke Dashboard</a>
            <a href="{{ route('address.create') }}" class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold py-1 px-3 rounded">Tambah Alamat</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-300 text-green-900 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($addresses as $address)
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="font-semibold">{{ $address->recipient_name }}</div>
                <div class="text-sm text-gray-600">No. Telp: {{ $address->phone }}</div>
                <div class="text-sm text-gray-600">Alamat: {{ $address->address }}</div>
                <div class="mt-3">
                    <a href="{{ route('address.edit', $address->id) }}" class="bg-gray-200 text-gray-800 px-3 py-1 rounded mr-2">Edit</a>
                    <form method="POST" action="{{ route('address.destroy', $address->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-gray-600">Belum ada alamat tersimpan.</div>
        @endforelse
    </div>
</div>
@endsection

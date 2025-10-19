@extends('dashboard.index')
@section('dashboard-content')
<div class="min-h-[60vh] flex items-start md:items-center justify-center py-8 md:py-16">
    <div class="w-full max-w-xl bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-semibold mb-6">Edit Alamat</h2>
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-300 text-yellow-900 px-4 py-3">
                <p class="font-semibold mb-2">Periksa kembali data kamu:</p>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('address.update', $address->id) }}">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', $address->recipient_name ?? '') }}" class="block w-full border-gray-200 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-blue-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telp</label>
                    <input type="text" name="phone" value="{{ old('phone', $address->phone ?? '') }}" class="block w-full border-gray-200 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-blue-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="4" class="block w-full border-gray-200 rounded-md shadow-sm py-2 px-3 focus:ring-2 focus:ring-blue-300" required>{{ old('address', $address->address ?? '') }}</textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium hover:bg-blue-700">Simpan Perubahan</button>
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:underline">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

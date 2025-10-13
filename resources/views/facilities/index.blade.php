@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Daftar Fasilitas</h1>
    <a href="{{ route('facilities.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Tambah
    </a>
</div>

@if(session('success'))
<div class="bg-green-100 text-green-700 p-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<table class="w-full border border-gray-200 text-black">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 border">Kode</th>
            <th class="p-2 border">Nama</th>
            <th class="p-2 border">Kategori</th>
            <th class="p-2 border">Lokasi</th>
            <th class="p-2 border">Kondisi</th>
            <th class="p-2 border">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facilities as $f)
        <tr>
            <td class="p-2 border">{{ $f->code }}</td>
            <td class="p-2 border">{{ $f->name }}</td>
            <td class="p-2 border">{{ $f->category->name }}</td>
            <td class="p-2 border">{{ $f->location->name }}</td>
            <td class="p-2 border">{{ ucfirst($f->condition) }}</td>
            <td class="p-2 border text-center">
                <a href="{{ route('facilities.edit', $f->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                <form action="{{ route('facilities.destroy', $f->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Yakin ingin hapus?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

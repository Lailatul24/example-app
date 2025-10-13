@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Tambah Fasilitas</h1>

<form action="{{ route('facilities.store') }}" method="POST" class="space-y-4 text-black">
    @csrf

    <div>
        <label>Kode</label>
        <input type="text" name="code" class="border p-2 w-full" required>
    </div>

    <div>
        <label>Nama</label>
        <input type="text" name="name" class="border p-2 w-full" required>
    </div>

    <div>
        <label>Kategori</label>
        <select name="category_id" class="border p-2 w-full" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Lokasi</label>
        <select name="location_id" class="border p-2 w-full" required>
            <option value="">-- Pilih Lokasi --</option>
            @foreach($locations as $loc)
            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Kuantitas Total</label>
        <input type="number" name="quantity_total" class="border p-2 w-full" required>
    </div>

    <div>
        <label>Kuantitas Tersedia</label>
        <input type="number" name="quantity_available" class="border p-2 w-full" required>
    </div>

    <div>
        <label>Kondisi</label>
        <select name="condition" class="border p-2 w-full">
            <option value="baik">Baik</option>
            <option value="rusak_ringan">Rusak Ringan</option>
            <option value="rusak_berat">Rusak Berat</option>
        </select>
    </div>

    <div>
        <label>Deskripsi</label>
        <textarea name="description" class="border p-2 w-full"></textarea>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
</form>
@endsection

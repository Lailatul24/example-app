@extends('layouts.app')

@section('content')
<h1>Daftar Fasilitas</h1>

<a href="{{ route('facilities.create') }}">Tambah Fasilitas</a>

<table border="1" cellpadding="5">
    <tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Kategori</th>
        <th>Lokasi</th>
        <th>Kondisi</th>
    </tr>
    @foreach($facilities as $facility)
    <tr>
        <td>{{ $facility->code }}</td>
        <td>{{ $facility->name }}</td>
        <td>{{ $facility->category->name }}</td>
        <td>{{ $facility->location->name }}</td>
        <td>{{ $facility->condition }}</td>
    </tr>
    @endforeach
</table>
@endsection

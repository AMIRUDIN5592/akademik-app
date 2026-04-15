@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3 mb-3">Selamat Datang, {{ $nama }}</h1>

            <p class="mb-1"><strong>Mata Kuliah:</strong> {{ $matkul }}</p>
            <p class="mb-1"><strong>Kampus:</strong> {{ $kampus }}</p>

            <hr>

            <h5>Daftar Materi</h5>
            <ul class="list-group">
                @foreach($daftarMateri as $materi)
                    <li class="list-group-item">{{ $materi }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection

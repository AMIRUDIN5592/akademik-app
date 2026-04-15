@extends('layouts.app')
@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3 mb-3">Halaman About</h1>
            <p class="mb-1"><strong>Nama Aplikasi:</strong> {{ $aplikasi }}</p>
            <p class="mb-1"><strong>Versi:</strong> {{ $versi }}</p>
            <p class="mb-1"><strong>Pembuat:</strong> {{ $pembuat }}</p>
        </div>
    </div>
@endsection

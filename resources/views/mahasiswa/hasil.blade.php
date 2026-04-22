@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Hasil Input Mahasiswa</h4>
        </div>
        <div class="card-body">
            @isset($data)
                <pre class="bg-light border rounded p-3">{{ print_r($data, true) }}</pre>
            @else
                <div class="alert alert-warning mb-0">
                    Belum ada data yang diproses.
                </div>
            @endisset

            <a href="{{ route('mahasiswa.form') }}" class="btn btn-primary mt-3">Kembali ke Form</a>
        </div>
    </div>
</div>
@endsection

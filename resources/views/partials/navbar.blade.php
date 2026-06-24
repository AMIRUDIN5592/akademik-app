@php
    use App\Helpers\RoleHelper;
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Akademik</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <div class="navbar-nav me-auto">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-gauge"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                    <i class="fas fa-info-circle"></i> About
                </a>
                @if (auth()->user()->role === 'admin')
                    <a class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}" href="{{ route('mahasiswa.index') }}">
                        <i class="fas fa-user-graduate"></i> Mahasiswa
                    </a>
                    <a class="nav-link {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}" href="{{ route('mata-kuliah.index') }}">
                        <i class="fas fa-book"></i> Mata Kuliah
                    </a>
                @endif
                @if (in_array(auth()->user()->role, ['admin', 'dosen'], true))
                    <a class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}" href="{{ route('nilai.index') }}">
                        <i class="fas fa-pen-to-square"></i> Nilai
                    </a>
                @endif
                @if (auth()->user()->role === 'mahasiswa')
                    <a class="nav-link {{ request()->routeIs('nilai-saya.*') ? 'active' : '' }}" href="{{ route('nilai-saya.index') }}">
                        <i class="fas fa-graduation-cap"></i> Nilai Saya
                    </a>
                @endif
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text text-white">
                    {{ auth()->user()->name }} | {{ RoleHelper::label(auth()->user()->role) }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

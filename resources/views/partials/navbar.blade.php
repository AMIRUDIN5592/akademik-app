<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">Laravel App</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <div class="navbar-nav ms-auto">
                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="/about">About</a>
            </div>
        </div>
    </div>
</nav>

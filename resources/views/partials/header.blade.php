<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" style="color: #024263;" href="{{ route('home') }}">
            <i class="bi bi-file-earmark-text-fill"></i> PDF<br>Croppers
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
            <ul class="navbar-nav ms-auto fw-bold">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('google.login') }}">Crop PDF</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('google.login') }}">E-Commerce</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('google.login') }}">All PDF Tools</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li>

                @auth
                <li class="nav-item d-flex align-items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn login-button">Logout</button>
                    </form>
                </li>
                @else
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="btn btn-primary login-button">Log in</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
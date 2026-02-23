<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Zip Api Client</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/js/app.js'])
</head>
<body class="min-vh-100 d-flex flex-column">

<nav class="navbar navbar-expand-lg border-bottom">
    <div class="container-lg">
        <span class="navbar-brand fw-semibold">
            Laravel Zip Api Client
        </span>

        <div class="d-flex align-items-center gap-2">
            <!-- Theme toggle -->
            <button id="theme-toggle" type="button" class="btn btn-outline-secondary btn-sm">
                <span id="theme-toggle-label">Light</span>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">
                        Dashboard
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<main class="flex-fill">
    <section class="container-lg py-5 py-lg-6">
        <div class="row g-4 text-center">
            <!-- Cities -->
            <div class="col-md-4">
                <div class="card shadow-sm p-4 rounded bg-primary-subtle">
                    <div class="display-4 fw-bold text-primary mb-2">
                        0
                    </div>
                    <p class="text-body-secondary fw-medium mb-0">
                        Cities in Database
                    </p>
                </div>
            </div>

            <!-- Counties -->
            <div class="col-md-4">
                <div class="card shadow-sm p-4 rounded bg-success-subtle">
                    <div class="display-4 fw-bold text-success mb-2">
                        0
                    </div>
                    <p class="text-body-secondary fw-medium mb-0">
                        Counties in Database
                    </p>
                </div>
            </div>

            <!-- Users -->
            <div class="col-md-4">
                <div class="card shadow-sm p-4 rounded bg-info-subtle">
                    <div class="display-4 fw-bold text-info mb-2">
                        0
                    </div>
                    <p class="text-body-secondary fw-medium mb-0">
                        Registered Users
                    </p>
                </div>
            </div>
        </div>
    </section>
    @auth
        <div class="text-center mt-3 small text-body-secondary">
            <span>{{ __("Go to") }}</span>
            <a href="{{ route('dashboard') }}" class="link-primary ms-1">
                {{ __('Dashboard') }}
            </a>
        </div>
    @endauth
</main>


<footer class="text-center py-4 small text-body-secondary">
    Made by Kristóf Áron as a school project
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    const toggle = document.getElementById('theme-toggle');
    const label = document.getElementById('theme-toggle-label');
    const html = document.documentElement;

    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const stored = localStorage.getItem('theme');

    function setTheme(theme) {
        html.setAttribute('data-bs-theme', theme);
        label.textContent = theme === 'dark' ? 'Dark' : 'Light';
    }

    // init
    if (stored) {
        setTheme(stored);
    } else {
        setTheme(prefersDark ? 'dark' : 'light');
    }

    toggle.addEventListener('click', () => {
        const current = html.getAttribute('data-bs-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        setTheme(next);
    });
})();
</script>

</body>
</html>

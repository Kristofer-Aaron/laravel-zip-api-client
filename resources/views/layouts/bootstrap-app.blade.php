<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laravel Zip Api Client')</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @livewireStyles
    <style>
        .card.rounded-lg { border-radius: 1rem; }
        .hover-shadow { transition: background-color .2s; }
        [data-bs-theme="light"] .hover-shadow:hover { background-color: var(--bs-gray-100); }
        [data-bs-theme="dark"] .hover-shadow:hover { background-color: var(--bs-gray-800); }
        /* Simple overlay modal replacement for Livewire */
        .lw-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
        .lw-modal { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; z-index:1050; }
    </style>
</head>
<body class="min-vh-100 d-flex flex-column">

<nav class="navbar navbar-expand-lg border-bottom">
    <div class="container-lg">
        <a href="{{ url('/') }}" class="navbar-brand fw-semibold">Laravel Zip Api Client</a>

        <div class="d-flex align-items-center gap-2">
            <button id="theme-toggle" type="button" class="btn btn-outline-secondary btn-sm">
                <span id="theme-toggle-label">Light</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Log out</button>
            </form>
        </div>
    </div>
</nav>

<main class="flex-fill py-5">
    <div class="container-lg">
        @yield('content')
    </div>
</main>

<footer class="text-center py-4 small text-body-secondary">
    Made by Kristóf Áron as a school project
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
<script>
(function () {
    const toggle = document.getElementById('theme-toggle');
    const label = document.getElementById('theme-toggle-label');
    const html = document.documentElement;

    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    function setTheme(theme) {
        html.setAttribute('data-bs-theme', theme);
        label.textContent = theme === 'dark' ? 'Dark' : 'Light';
    }

    setTheme(stored ?? (prefersDark ? 'dark' : 'light'));

    toggle.addEventListener('click', () => {
        const next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', next);
        setTheme(next);
    });
})();
</script>
</body>
</html>
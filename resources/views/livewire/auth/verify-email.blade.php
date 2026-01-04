<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Development – Laravel Zip Api Client</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="min-vh-100 d-flex flex-column">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg border-bottom">
    <div class="container-lg">
        <a href="{{ url('/') }}" class="navbar-brand fw-semibold">
            Laravel Zip Api Client
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Theme toggle -->
            <button
                id="theme-toggle"
                type="button"
                class="btn btn-outline-secondary btn-sm"
            >
                <span id="theme-toggle-label">Light</span>
            </button>

            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                Log in
            </a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                Sign up
            </a>
        </div>
    </div>
</nav>

<!-- Main -->
<main class="flex-fill d-flex align-items-center justify-content-center">
    <div class="container-sm py-5">
        <div class="mx-auto" style="max-width: 500px;">
            <!-- Card -->
            <div class="card shadow-sm text-center">
                <div class="card-body p-5">
                    <h1 class="h2 fw-bold mb-3">Under Development</h1>
                    <p class="text-body-secondary mb-4">
                        This page or feature is currently under development. Please check back soon!
                    </p>

                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                        Back to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-4 small text-body-secondary">
    Made by Kristóf Áron as a school project
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Styles -->
<style>
    .card {
        border-radius: 1rem;
    }

    /* Outlined panel for placeholders */
    .input-panel-outline {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.75rem;
        background-color: transparent;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    [data-bs-theme="dark"] .input-panel-outline {
        border-color: var(--bs-border-color);
    }
</style>

<!-- Theme toggle -->
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


<!-- <x-layouts.auth>
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </flux:text>

        @if (session('status') == 'verification-link-sent')
            <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </flux:text>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Resend verification email') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
               <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth> -->

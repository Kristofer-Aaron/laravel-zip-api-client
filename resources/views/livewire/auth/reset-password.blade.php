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
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <flux:input
                name="email"
                value="{{ request('email') }}"
                :label="__('Email')"
                type="email"
                required
                autocomplete="email"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="reset-password-button">
                    {{ __('Reset password') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth> -->

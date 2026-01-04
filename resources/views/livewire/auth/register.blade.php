<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register – Laravel Zip Api Client</title>

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
        </div>
    </div>
</nav>

<!-- Main -->
<main class="flex-fill">
    <div class="container-sm py-5">
        <div class="mx-auto" style="max-width: 460px;">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="h3 fw-semibold mb-2">
                    {{ __('Create an account') }}
                </h1>
                <p class="text-body-secondary">
                    {{ __('Enter your details below to create your account') }}
                </p>
            </div>

            <!-- Card -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                {{ __('Name') }}
                            </label>
                            <div class="input-panel-outline @error('name') is-invalid @enderror">
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-control border-0 bg-transparent px-0"
                                    placeholder="{{ __('Full name') }}"
                                    required
                                    autofocus
                                    autocomplete="name"
                                >
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                {{ __('Email address') }}
                            </label>
                            <div class="input-panel-outline @error('email') is-invalid @enderror">
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-control border-0 bg-transparent px-0"
                                    placeholder="email@example.com"
                                    required
                                    autocomplete="email"
                                >
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                {{ __('Password') }}
                            </label>
                            <div class="input-panel-outline @error('password') is-invalid @enderror">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control border-0 bg-transparent px-0"
                                    placeholder="{{ __('Password') }}"
                                    required
                                    autocomplete="new-password"
                                >
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                {{ __('Confirm password') }}
                            </label>
                            <div class="input-panel-outline">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control border-0 bg-transparent px-0"
                                    placeholder="{{ __('Confirm password') }}"
                                    required
                                    autocomplete="new-password"
                                >
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2">
                                {{ __('Create account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer link -->
            <div class="text-center mt-3 small text-body-secondary">
                <span>{{ __('Already have an account?') }}</span>
                <a href="{{ route('login') }}" class="link-primary ms-1">
                    {{ __('Log in') }}
                </a>
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

    /* Outlined input panels (like Light/Dark button) */
    .input-panel-outline {
        padding: 0.75rem 0.9rem;
        border-radius: 0.75rem;
        border: 1px solid var(--bs-border-color);
        background-color: transparent;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .input-panel-outline:focus-within {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
    }

    .input-panel-outline.is-invalid {
        border-color: var(--bs-danger);
    }

    .input-panel-outline .form-control:focus {
        box-shadow: none;
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

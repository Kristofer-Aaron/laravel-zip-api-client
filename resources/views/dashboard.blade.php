<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard – Laravel Zip Api Client</title>

    <!-- Bootstrap 5.3 link -->
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
            <button id="theme-toggle" type="button" class="btn btn-outline-secondary btn-sm">
                <span id="theme-toggle-label">Light</span>
            </button>

            @if(session('api_token'))
                <form method="POST" action="{{ route('api.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">Logout API</button>
                </form>
            @else
                <a href="{{ route('api.login.form') }}" class="btn btn-info btn-sm">Login to API</a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Log out</button>
            </form>
        </div>
    </div>
</nav>

<!-- Main -->
<main class="flex-fill py-5">
    <div class="container-lg">

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <!-- Total Cities -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border rounded-lg">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-body-secondary mb-1">Total Cities</p>
                            <h3 class="fw-bold">0</h3> <!-- Fetch from API if needed -->
                        </div>
                        <div class="rounded-lg bg-subtle p-3">
                            <i class="bi bi-building" style="font-size: 1.5rem; color:#0d6efd;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Counties -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border rounded-lg">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-body-secondary mb-1">Total Counties</p>
                            <h3 class="fw-bold">0</h3> <!-- Fetch from API if needed -->
                        </div>
                        <div class="rounded-lg bg-subtle p-3">
                            <i class="bi bi-map" style="font-size: 1.5rem; color:#198754;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100 border rounded-lg">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-body-secondary mb-1">Total Users</p>
                            <h3 class="fw-bold">0</h3> <!-- Fetch from API if needed -->
                        </div>
                        <div class="rounded-lg bg-subtle p-3">
                            <i class="bi bi-people" style="font-size: 1.5rem; color:#0dcaf0;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Resources -->
        <div class="card shadow-sm rounded-lg mb-5">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-4">Manage Resources</h2>
                <div class="row g-3">
                        <div class="col-sm-6">
                        <a href="{{ route('cities-view.index') }}" class="card h-100 text-decoration-none border border-1 hover-shadow">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-lg bg-subtle p-2">
                                    <i class="bi bi-building" style="color:#0d6efd;"></i>
                                </div>
                                <div>
                                    <p class="mb-1 fw-medium">Manage Cities</p>
                                    <p class="text-body-secondary small mb-0">View and manage all cities</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6">
                        <a href="{{ route('counties-view.index') }}" class="card h-100 text-decoration-none border border-1 hover-shadow">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-lg bg-subtle p-2">
                                    <i class="bi bi-map" style="color:#198754;"></i>
                                </div>
                                <div>
                                    <p class="mb-1 fw-medium">Manage Counties</p>
                                    <p class="text-body-secondary small mb-0">View and manage all counties</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cities -->
        <div class="card shadow-sm rounded-lg">
            <div class="card-body">
                <h2 class="h5 fw-semibold mb-4">Recently Added Cities</h2>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Zip</th>
                                <th>City</th>
                                <th>County</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ([] as $city)
                                <tr>
                                    <td><code class="bg-light px-2 py-1 rounded">{{ $city->zip }}</code></td>
                                    <td>{{ $city->name }}</td>
                                    <td><span class="badge bg-primary">{{ $city->county->name }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-body-secondary py-4">No cities yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<footer class="text-center py-4 small text-body-secondary">
    Made by Kristóf Áron as a school project
</footer>

<!-- Bootstrap JS + icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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

<!-- Custom Styles -->
<style>
    .card.rounded-lg {
        border-radius: 1rem;
    }

    /* Light mode hover */
    [data-bs-theme="light"] .hover-shadow:hover {
        background-color: var(--bs-gray-100);
    }

    /* Dark mode hover */
    [data-bs-theme="dark"] .hover-shadow:hover {
        background-color: var(--bs-gray-800);
    }
</style>

</body>
</html>

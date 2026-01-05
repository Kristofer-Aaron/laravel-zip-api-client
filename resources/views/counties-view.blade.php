@extends('layouts.bootstrap-app')

@section('title', __('Counties'))

@section('content')
<div class="container-lg py-4">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="h3 fw-semibold">{{ __('Counties') }}</h1>
            <p class="text-muted small">{{ __('Manage all counties in the system') }}</p>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <!-- Open Create Modal -->
            <button type="button" class="btn btn-primary rounded-lg shadow-sm" onclick="openCreateModal()">
                <i class="bi bi-plus"></i> {{ __('Add County') }}
            </button>
        </div>
    </div>

    <div class="mb-3 d-flex gap-2">
        <form action="{{ route('counties-view.export') }}" method="GET">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" name="type" value="csv" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </button>
            <button type="submit" name="type" value="pdf" class="btn btn-outline-secondary" disabled>
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </button>
        </form>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <!-- Counties Table -->
    <div class="card shadow-sm rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th style="width:160px">{{ __('Cities Count') }}</th>
                            <th class="text-end" style="width:140px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($counties as $county)
                            <tr>
                                <td class="fw-medium">{{ $county->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $county->cities_count }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <!-- Open Edit Modal -->
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                onclick="openEditModal({{ $county->id }}, '{{ $county->name }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('counties-view.destroy', $county->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    {{ __('No counties found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $counties->links('pagination::simple-bootstrap-5') }}
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createCountyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('counties-view.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create New County') }}</h5>
                    <button type="button" class="btn-close" onclick="closeCreateModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('County Name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Enter county name') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" onclick="closeCreateModal()">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create County') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCountyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCountyForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit County') }}</h5>
                    <button type="button" class="btn-close" onclick="closeEditModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('County Name') }}</label>
                        <input type="text" name="name" id="editCountyName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" onclick="closeEditModal()">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Scripts -->
<script>
    function openCreateModal() {
        new bootstrap.Modal(document.getElementById('createCountyModal')).show();
    }
    function closeCreateModal() {
        bootstrap.Modal.getInstance(document.getElementById('createCountyModal')).hide();
    }

    function openEditModal(id, name) {
        const form = document.getElementById('editCountyForm');
        form.action = `/counties/${id}`;
        document.getElementById('editCountyName').value = name;
        new bootstrap.Modal(document.getElementById('editCountyModal')).show();
    }
    function closeEditModal() {
        bootstrap.Modal.getInstance(document.getElementById('editCountyModal')).hide();
    }
</script>

@endsection

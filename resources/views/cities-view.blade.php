@extends('layouts.bootstrap-app')

@section('title', __('Cities'))

@section('content')
<div class="container-lg py-4">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="h3 fw-semibold">{{ __('Cities') }}</h1>
            <p class="text-muted small">{{ __('Manage all cities in the system') }}</p>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <!-- Open Create Modal -->
            <button type="button" class="btn btn-primary rounded-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createCityModal">
                <i class="bi bi-plus"></i> {{ __('Add City') }}
            </button>
        </div>
        
        <div class="mb-3 d-flex gap-2">
            <form action="{{ route('cities-view.export') }}" method="GET">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="county_filter" value="{{ request('county_filter') }}">
                <input type="hidden" name="letter" value="{{ request('letter') }}">
                <button type="submit" name="type" value="csv" class="btn btn-outline-secondary">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                </button>
                <button type="submit" name="type" value="pdf" class="btn btn-outline-secondary" disabled>
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
            </form>
        </div>


    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <!-- Search & County Filter -->
<div class="card mb-4 shadow-sm rounded-lg">
    <div class="card-body">
        <form method="GET" action="{{ route('cities-view.index') }}" class="row g-3" id="filter-form">
            <div class="col-md-6">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    class="form-control rounded-lg" 
                    placeholder="{{ __('Search by name or zip...') }}"
                    oninput="document.getElementById('filter-form').submit()"
                >
            </div>
            <div class="col-md-6">
                <select 
                    name="county_filter" 
                    class="form-select rounded-lg" 
                    onchange="document.getElementById('filter-form').submit()"
                >
                    <option value="">{{ __('All Counties') }}</option>
                    @foreach ($counties as $county)
                        <option 
                            value="{{ $county->id }}" 
                            {{ request('county_filter') == $county->id ? 'selected' : '' }}
                        >
                            {{ $county->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>


    <!-- Alphabetical Filter -->
    <div class="card mb-4 shadow-sm rounded-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-medium">{{ __('Filter by initial letter') }}</span>
                <a href="{{ route('cities-view.index') }}" class="btn btn-link btn-sm">{{ __('Clear') }}</a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $hungarian = ['A','Á','B','C','D','E','É','F','G','H','I','Í','J','K','L','M','N','O','Ó','Ö','Ő','P','Q','R','S','T','U','Ú','Ü','Ű','V','W','X','Y','Z'];
                @endphp
                @foreach ($hungarian as $letter)
                    <a href="{{ route('cities-view.index', array_merge(request()->except('page'), ['letter' => $letter])) }}"
                       class="btn btn-sm rounded-2 {{ request('letter') === $letter ? 'btn-primary text-white' : 'btn-outline-secondary' }}">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Cities Table -->
    <div class="card shadow-sm rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:12%">{{ __('Zip') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('County') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cities as $city)
                            <tr>
                                <td><code class="bg-light px-2 py-1 rounded">{{ $city->zip }}</code></td>
                                <td>{{ $city->name }}</td>
                                <td><span class="badge bg-primary">{{ $city->county->name }}</span></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <!-- Open Edit Modal -->
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCityModal{{ $city->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- Delete Form -->
                                        <form action="{{ route('cities-view.destroy', $city->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCityModal{{ $city->id }}" tabindex="-1" aria-labelledby="editCityModalLabel{{ $city->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content rounded-lg shadow">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCityModalLabel{{ $city->id }}">{{ __('Edit City') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('cities-view.update', $city->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Zip Code') }}</label>
                                                    <input type="text" name="zip" class="form-control" value="{{ $city->zip }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('City Name') }}</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $city->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('County') }}</label>
                                                    <select name="county_id" class="form-select" required>
                                                        @foreach ($counties as $county)
                                                            <option value="{{ $county->id }}" {{ $city->county_id == $county->id ? 'selected' : '' }}>
                                                                {{ $county->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">{{ __('No cities found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $cities->appends(request()->except('page'))->links() }}
    </div>
</div>

<!-- Create City Modal -->
<div class="modal fade" id="createCityModal" tabindex="-1" aria-labelledby="createCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-lg shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="createCityModalLabel">{{ __('Create New City') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('cities-view.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Zip Code') }}</label>
                        <input type="text" name="zip" class="form-control" placeholder="1234" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('City Name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Enter city name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('County') }}</label>
                        <select name="county_id" class="form-select" required>
                            <option value="">{{ __('Select a county') }}</option>
                            @foreach($counties as $county)
                                <option value="{{ $county->id }}">{{ $county->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create City') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

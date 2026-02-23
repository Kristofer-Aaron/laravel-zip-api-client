<div>
    <!-- Cities Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th wire:click="sort('zip')" style="cursor: pointer;">
                        ZIP Code
                        @if ($sort === 'zip')
                            <i class="bi bi-chevron-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sort('name')" style="cursor: pointer;">
                        City Name
                        @if ($sort === 'name')
                            <i class="bi bi-chevron-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>County</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cities as $city)
                    <tr>
                        <td>{{ $city['zip'] }}</td>
                        <td>{{ $city['name'] }}</td>
                        <td>{{ $city['county']['name'] ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" wire:click="openEditModal({{ $city['id'] }})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger ms-1" wire:click="delete({{ $city['id'] }})"
                                    onclick="return confirm('Are you sure you want to delete this city?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No cities found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($cities->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $cities->links() }}
        </div>
    @endif

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" wire:model="form.zip" required>
                            @error('form.zip') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City Name</label>
                            <input type="text" class="form-control" wire:model="form.name" required>
                            @error('form.name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">County</label>
                            <select class="form-select" wire:model="form.county_id" required>
                                <option value="">Select County</option>
                                @foreach ($counties as $county)
                                    <option value="{{ $county['id'] }}">{{ $county['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form.county_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" wire:model="form.zip" required>
                            @error('form.zip') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City Name</label>
                            <input type="text" class="form-control" wire:model="form.name" required>
                            @error('form.name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">County</label>
                            <select class="form-select" wire:model="form.county_id" required>
                                <option value="">Select County</option>
                                @foreach ($counties as $county)
                                    <option value="{{ $county['id'] }}">{{ $county['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form.county_id') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success mt-3">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:loaded', () => {
    Livewire.on('showCreateModal', () => {
        const modal = new bootstrap.Modal(document.getElementById('createModal'));
        modal.show();
    });

    Livewire.on('closeCreateModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
        if (modal) modal.hide();
    });

    Livewire.on('showEditModal', () => {
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    });

    Livewire.on('closeEditModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        if (modal) modal.hide();
    });
});
</script>
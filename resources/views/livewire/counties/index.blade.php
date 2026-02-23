<div>
    <!-- Counties Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th wire:click="sort('name')" style="cursor: pointer;">
                        County Name
                        @if ($sort === 'name')
                            <i class="bi bi-chevron-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Cities Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($counties as $county)
                    <tr>
                        <td>{{ $county->name }}</td>
                        <td>{{ $county->cities_count }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" wire:click="openEditModal({{ $county->id }})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger ms-1" wire:click="delete({{ $county->id }})"
                                    onclick="return confirm('Are you sure you want to delete this county?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No counties found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($counties->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $counties->links() }}
        </div>
    @endif

    <!-- Create Modal -->
    <div class="modal fade {{ $showCreateModal ? 'show d-block' : '' }}" id="createModal" tabindex="-1" style="{{ $showCreateModal ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create County</h5>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label">County Name</label>
                            <input type="text" class="form-control" wire:model="form.name" required>
                            @error('form.name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if ($showCreateModal)
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Edit Modal -->
    <div class="modal fade {{ $showEditModal ? 'show d-block' : '' }}" id="editModal" tabindex="-1" style="{{ $showEditModal ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit County</h5>
                    <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label">County Name</label>
                            <input type="text" class="form-control" wire:model="form.name" required>
                            @error('form.name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if ($showEditModal)
        <div class="modal-backdrop fade show"></div>
    @endif

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
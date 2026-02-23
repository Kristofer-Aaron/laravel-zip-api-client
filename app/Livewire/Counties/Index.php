<?php

namespace App\Livewire\Counties;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\County;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sort = 'name';
    public $direction = 'asc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;

    public $form = [
        'name' => '',
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = County::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $query->orderBy($this->sort, $this->direction);

        $counties = $query->withCount('cities')->paginate(15);

        return view('livewire.counties.index', [
            'counties' => $counties,
        ]);
    }

    public function sort($field)
    {
        if ($this->sort === $field) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $field;
            $this->direction = 'asc';
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($id)
    {
        $county = County::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'name' => $county->name,
        ];
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate([
            'form.name' => 'required|string|max:255|unique:counties,name',
        ]);

        County::create([
            'name' => $this->form['name'],
        ]);

        $this->closeCreateModal();
        session()->flash('message', 'County created successfully!');
    }

    public function update()
    {
        $this->validate([
            'form.name' => 'required|string|max:255|unique:counties,name,' . $this->editingId,
        ]);

        $county = County::findOrFail($this->editingId);
        $county->update([
            'name' => $this->form['name'],
        ]);

        $this->closeEditModal();
        session()->flash('message', 'County updated successfully!');
    }

    public function delete($id)
    {
        $county = County::findOrFail($id);
        
        if ($county->cities()->count() > 0) {
            session()->flash('error', 'Cannot delete county with associated cities!');
            return;
        }

        $county->delete();
        session()->flash('message', 'County deleted successfully!');
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
        ];
        $this->editingId = null;
    }
}

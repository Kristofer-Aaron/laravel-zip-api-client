<?php

namespace App\Livewire\Cities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\City;
use App\Models\County;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $county_filter = '';
    public $selectedLetters = [];
    public $sort = 'name';
    public $direction = 'asc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;

    public $form = [
        'zip' => '',
        'name' => '',
        'county_id' => '',
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCountyFilter()
    {
        $this->resetPage();
    }

    public function updatingSelectedLetters()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = City::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('zip', 'like', '%' . $this->search . '%');
        }

        if ($this->county_filter) {
            $query->where('county_id', $this->county_filter);
        }

        // Filter by starting letters (Hungarian alphabet digraphs supported)
        if (!empty($this->selectedLetters)) {
            $query->where(function ($q) {
                foreach ($this->selectedLetters as $letter) {
                    $q->orWhere('name', 'like', $letter . '%');
                }
            });
        }

        $query->orderBy($this->sort, $this->direction);

        $cities = $query->with('county')->paginate(15);
        $counties = County::orderBy('name')->get();

        return view('livewire.cities.index', [
            'cities' => $cities,
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
        $city = City::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'zip' => $city->zip,
            'name' => $city->name,
            'county_id' => $city->county_id,
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
            'form.zip' => 'required|string|max:10',
            'form.name' => 'required|string|max:255',
            'form.county_id' => 'required|exists:counties,id',
        ]);

        City::create([
            'zip' => $this->form['zip'],
            'name' => $this->form['name'],
            'county_id' => $this->form['county_id'],
        ]);

        $this->closeCreateModal();
        session()->flash('message', 'City created successfully!');
    }

    public function update()
    {
        $this->validate([
            'form.zip' => 'required|string|max:10',
            'form.name' => 'required|string|max:255',
            'form.county_id' => 'required|exists:counties,id',
        ]);

        $city = City::findOrFail($this->editingId);
        $city->update([
            'zip' => $this->form['zip'],
            'name' => $this->form['name'],
            'county_id' => $this->form['county_id'],
        ]);

        $this->closeEditModal();
        session()->flash('message', 'City updated successfully!');
    }

    public function delete($id)
    {
        City::findOrFail($id)->delete();
        session()->flash('message', 'City deleted successfully!');
    }

    private function resetForm()
    {
        $this->form = [
            'zip' => '',
            'name' => '',
            'county_id' => '',
        ];
        $this->editingId = null;
    }
}

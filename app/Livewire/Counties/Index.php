<?php

namespace App\Livewire\Counties;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

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
        $token = session('api_token');
        if (!$token) {
            return view('livewire.counties.index', [
                'counties' => collect(),
            ])->with('error', 'Please login to API first');
        }

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->get($apiBase . '/counties');
        $countiesData = collect($response->json());

        $query = $countiesData;

        if ($this->search) {
            $query = $query->filter(function ($county) {
                return str_contains(strtolower($county['name']), strtolower($this->search));
            });
        }

        $query = $query->sortBy($this->sort, SORT_REGULAR, $this->direction === 'desc');

        $counties = $query->map(function ($county) {
            $county['cities_count'] = 0; // Placeholder
            return (object) $county;
        })->paginate(15);

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
        $token = session('api_token');
        if (!$token) {
            session()->flash('error', 'Please login to API first');
            return;
        }

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->get($apiBase . '/counties/' . $id);
        if ($response->successful()) {
            $county = $response->json();
            $this->editingId = $id;
            $this->form = [
                'name' => $county['name'],
            ];
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $token = session('api_token');
        if (!$token) {
            session()->flash('error', 'Please login to API first');
            return;
        }

        $this->validate([
            'form.name' => 'required|string|max:255',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->post($apiBase . '/counties', $this->form);

        if ($response->successful()) {
            $this->closeCreateModal();
            session()->flash('message', 'County created successfully!');
        } else {
            session()->flash('error', 'Failed to create county');
        }
    }

    public function update()
    {
        $token = session('api_token');
        if (!$token) {
            session()->flash('error', 'Please login to API first');
            return;
        }

        $this->validate([
            'form.name' => 'required|string|max:255',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->put($apiBase . '/counties/' . $this->editingId, $this->form);

        if ($response->successful()) {
            $this->closeEditModal();
            session()->flash('message', 'County updated successfully!');
        } else {
            session()->flash('error', 'Failed to update county');
        }
    }

    public function delete($id)
    {
        $token = session('api_token');
        if (!$token) {
            session()->flash('error', 'Please login to API first');
            return;
        }

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->delete($apiBase . '/counties/' . $id);

        if ($response->successful()) {
            session()->flash('message', 'County deleted successfully!');
        } else {
            session()->flash('error', 'Failed to delete county');
        }
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
        ];
        $this->editingId = null;
    }
}

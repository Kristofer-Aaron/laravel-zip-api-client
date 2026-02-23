<?php

namespace App\Livewire\Cities;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;

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
        $token = session('api_token');
        if (!$token) {
            return view('livewire.cities.index', [
                'cities' => collect(),
                'counties' => collect(),
            ])->with('error', 'Please login to API first');
        }

        $apiBase = config('services.api.base_uri');

        $citiesResponse = Http::withToken($token)->get($apiBase . '/cities');
        $citiesData = collect($citiesResponse->json());

        $countiesResponse = Http::withToken($token)->get($apiBase . '/counties');
        $countiesData = collect($countiesResponse->json());

        $query = $citiesData;

        if ($this->search) {
            $query = $query->filter(function ($city) {
                return str_contains(strtolower($city['name']), strtolower($this->search)) ||
                       str_contains($city['zip'], $this->search);
            });
        }

        if ($this->county_filter) {
            $query = $query->where('county_id', $this->county_filter);
        }

        // Filter by starting letters
        if (!empty($this->selectedLetters)) {
            $query = $query->filter(function ($city) {
                foreach ($this->selectedLetters as $letter) {
                    if (str_starts_with(strtolower($city['name']), strtolower($letter))) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Sort
        $query = $query->sortBy($this->sort, SORT_REGULAR, $this->direction === 'desc');

        $cities = $query->paginate(15);
        $counties = $countiesData->sortBy('name');

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
        $token = session('api_token');
        if (!$token) {
            session()->flash('error', 'Please login to API first');
            return;
        }

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->get($apiBase . '/cities/' . $id);
        if ($response->successful()) {
            $city = $response->json();
            $this->editingId = $id;
            $this->form = [
                'zip' => $city['zip'],
                'name' => $city['name'],
                'county_id' => $city['county_id'],
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
            'form.zip' => 'required|string|max:10',
            'form.name' => 'required|string|max:255',
            'form.county_id' => 'required|integer',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->post($apiBase . '/cities', $this->form);

        if ($response->successful()) {
            $this->closeCreateModal();
            session()->flash('message', 'City created successfully!');
        } else {
            session()->flash('error', 'Failed to create city');
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
            'form.zip' => 'required|string|max:10',
            'form.name' => 'required|string|max:255',
            'form.county_id' => 'required|integer',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->put($apiBase . '/cities/' . $this->editingId, $this->form);

        if ($response->successful()) {
            $this->closeEditModal();
            session()->flash('message', 'City updated successfully!');
        } else {
            session()->flash('error', 'Failed to update city');
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
        $response = Http::withToken($token)->delete($apiBase . '/cities/' . $id);

        if ($response->successful()) {
            session()->flash('message', 'City deleted successfully!');
        } else {
            session()->flash('error', 'Failed to delete city');
        }
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

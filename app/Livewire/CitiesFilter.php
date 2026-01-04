<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\County;
use App\Models\City;

class CitiesFilter extends Component
{
    public $counties;
    public $selectedCounty = null;
    public $selectedLetter = null;
    public $letters = [];

    public function mount()
    {
        $this->counties = County::orderBy('name')->get();
        $this->letters = range('A', 'Z');
    }

    public function render()
    {
        $cities = collect();

        if ($this->selectedCounty) {
            $query = City::where('county_id', $this->selectedCounty);

            if ($this->selectedLetter) {
                $query->where('name', 'like', $this->selectedLetter . '%');
            }

            $cities = $query->orderBy('name')->get();
        }

        return view('livewire.cities-filter', [
            'cities' => $cities,
        ]);
    }

    public function selectLetter($letter)
    {
        $this->selectedLetter = $letter;
    }
}

<div class="p-4">

    <!-- County dropdown -->
    <div class="mb-4">
        <label class="block font-semibold mb-1">Select County:</label>
        <select wire:model="selectedCounty" class="border rounded p-2 w-full">
            <option value="">-- Choose County --</option>
            @foreach($counties as $county)
                <option value="{{ $county->id }}">{{ $county->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Alphabet letters -->
    @if($selectedCounty)
        <div class="mb-4 flex flex-wrap gap-2">
            @php
                $lettersWithCities = City::where('county_id', $selectedCounty)
                    ->selectRaw('UPPER(LEFT(name,1)) as first_letter')
                    ->distinct()
                    ->pluck('first_letter')
                    ->toArray();
            @endphp

            @foreach($letters as $letter)
                <button 
                    wire:click="selectLetter('{{ $letter }}')"
                    class="px-2 py-1 border rounded 
                        {{ in_array($letter, $lettersWithCities) ? 'text-green-600 font-bold' : 'text-gray-400' }}
                        {{ $selectedLetter === $letter ? 'bg-gray-200' : '' }}">
                    {{ $letter }}
                </button>
            @endforeach
        </div>
    @endif

    <!-- Cities table -->
    @if($selectedCounty)
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">ZIP</th>
                    <th class="border px-2 py-1">City</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cities as $city)
                    <tr>
                        <td class="border px-2 py-1">{{ $city->zip }}</td>
                        <td class="border px-2 py-1">{{ $city->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="border px-2 py-1 text-center">No cities found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>

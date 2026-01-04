<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\County;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // -------------------
    // API Methods (JSON)
    // -------------------
    public function index()
    {
        return response()->json(City::with('county')->get(), 200);
    }

    public function show(int $id)
    {
        $city = City::with('county')->find($id);
        if (!$city) {
            return response()->json(['message' => 'City with id not found'], 404);
        }
        return response()->json($city, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county' => 'required|string|max:255',
        ]);

        $county = County::firstOrCreate(['name' => $data['county']]);

        $city = City::create([
            'zip' => $data['zip'],
            'name' => $data['name'],
            'county_id' => $county->id,
        ]);

        return response()->json($city->load('county'), 201);
    }

    public function update(Request $request, int $id)
    {
        $city = City::with('county')->find($id);
        if (!$city) {
            return response()->json(['message' => 'City with id not found'], 404);
        }

        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county' => 'required|string|max:255',
        ]);

        $county = County::firstOrCreate(['name' => $data['county']]);

        $city->update([
            'zip' => $data['zip'],
            'name' => $data['name'],
            'county_id' => $county->id,
        ]);

        return response()->json($city->load('county'), 200);
    }

    public function destroy(int $id)
    {
        $city = City::with('county')->find($id);
        if (!$city) {
            return response()->json(['message' => 'City with id not found'], 404);
        }

        $city->delete();
        return response()->json(null, 204);
    }

    // -------------------
    // Web Methods (Blade)
    // -------------------

    // Index page with filters
    public function webIndex(Request $request)
    {
        $counties = County::orderBy('name')->get();
        $query = City::with('county');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('zip', 'like', "%{$search}%");
            });
        }

        // County filter
        if ($countyId = $request->input('county_filter')) {
            $query->where('county_id', $countyId);
        }

        // Alphabetical filter
        if ($letter = $request->input('letter')) {
            $query->where('name', 'like', strtoupper($letter) . '%');
        }

        $cities = $query->orderBy('name')->simplePaginate(25)->withQueryString();

        return view('cities-view', [
            'cities' => $cities,
            'counties' => $counties,
        ]);
    }

    // Web Create (from form)
    public function webStore(Request $request)
    {
        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
        ]);

        City::create([
            'zip' => $data['zip'],
            'name' => $data['name'],
            'county_id' => $data['county_id'],
        ]);

        return redirect()->route('cities-view.index')->with('message', 'City created successfully');
    }

    // Web Update (from form)
    public function webUpdate(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
        ]);

        $city->update($data);

        return redirect()->route('cities-view.index')->with('message', 'City updated successfully');
    }

    // Web Destroy
    public function webDestroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return redirect()->route('cities-view.index')->with('message', 'City deleted successfully');
    }
}

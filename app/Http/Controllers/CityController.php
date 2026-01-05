<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\County;
use Illuminate\Http\Request;

// PDF export dependencies
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * @apiDefine CityGroup Cities
 * @apiDescription API endpoints for managing cities and their counties.
 */
class CityController extends Controller
{
    // -------------------
    // API Methods (JSON)
    // -------------------
    /**
     * @api {get} /api/cities List Cities
     * @apiName GetCities
     * @apiGroup CityGroup
     * @apiSuccess {Object[]} cities List of cities
     * @apiSuccess {Number} cities.id City unique ID
     * @apiSuccess {String} cities.zip Postal code
     * @apiSuccess {String} cities.name City name
     * @apiSuccess {Object} cities.county Associated county object
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * [
     *   {"id":1,"zip":"1000","name":"Sample","county":{...}}
     * ]
     */
    public function index()
    {
        return response()->json(City::with('county')->get(), 200);
    }

    /**
     * @api {get} /api/cities/:id Get City
     * @apiName GetCity
     * @apiGroup CityGroup
     * @apiParam {Number} id City's unique ID.
     * @apiSuccess {Number} id City unique ID
     * @apiSuccess {String} zip Postal code
     * @apiSuccess {String} name City name
     * @apiSuccess {Object} county Associated county
     * @apiError {String} message Error message when not found
     */
    public function show(int $id)
    {
        $city = City::with('county')->find($id);
        if (!$city) {
            return response()->json(['message' => 'City with id not found'], 404);
        }
        return response()->json($city, 200);
    }

    /**
     * @api {post} /api/cities Create City
     * @apiName CreateCity
     * @apiGroup CityGroup
     * @apiParam {String} zip Postal code (4 digits)
     * @apiParam {String} name City name
     * @apiParam {String} county County name (will be created if missing)
     * @apiSuccess (Created 201) {Number} id Created city ID
     * @apiSuccessExample {json} Created-Response:
     * HTTP/1.1 201 Created
     * {"id":123,"zip":"1000","name":"New City","county":{...}}
     */
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

    /**
     * @api {put} /api/cities/:id Update City
     * @apiName UpdateCity
     * @apiGroup CityGroup
     * @apiParam {Number} id City ID
     * @apiParam {String} zip Postal code (4 digits)
     * @apiParam {String} name City name
     * @apiParam {String} county County name
     * @apiSuccess {Object} city Updated city object
     */
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

    /**
     * @api {delete} /api/cities/:id Delete City
     * @apiName DeleteCity
     * @apiGroup CityGroup
     * @apiParam {Number} id City ID
     * @apiSuccess (No Content 204) - No response body
     */
    public function destroy(int $id)
    {
        $city = City::with('county')->find($id);
        if (!$city) {
            return response()->json(['message' => 'City with id not found'], 404);
        }

        $city->delete();
        return response()->json(null, 204);
    }

    // Controller
    public function export(Request $request)
    {
        $query = City::with('county');

        // Filters
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('zip', 'like', "%{$search}%");
            });
        }

        if ($countyId = $request->input('county_filter')) {
            $query->where('county_id', $countyId);
        }

        if ($letter = $request->input('letter')) {
            $query->where('name', 'like', strtoupper($letter) . '%');
        }

        $cities = $query->orderBy('name')->get();

        // Decide export type
        if ($request->input('type') === 'pdf') {
            return $this->exportPDF($cities);   // private helper
        } else {
            return $this->exportCSV($cities);   // private helper
        }
    }

    // Private helpers
    private function exportCSV($cities)
    {
        $filename = 'cities.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8-hungarian-ci',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($cities) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper encoding recognition
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Zip', 'Name', 'County']); // Header row
            foreach ($cities as $city) {
                fputcsv($handle, [$city->zip, $city->name, $city->county->name]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPDF($cities)
    {
        // Make sure $cities is a collection
        if ($cities instanceof \Illuminate\Database\Eloquent\Builder) {
            $cities = $cities->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.cities-pdf', compact('cities'));
        return $pdf->download('cities.pdf');
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

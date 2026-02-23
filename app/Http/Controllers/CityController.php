<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// PDF export dependencies
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * @apiDefine CityGroup Cities
 * @apiDescription API endpoints for managing cities and their counties.
 */
class CityController extends Controller
{
    /* =====================================================
     | API METHODS (JSON)
     |=====================================================*/

    /**
     * @api {get} /api/cities List Cities
     * @apiName GetCities
     * @apiGroup CityGroup
     * @apiSuccess {Object[]} cities List of cities
     * @apiSuccess {Number} cities.id City ID
     * @apiSuccess {String} cities.zip City ZIP code
     * @apiSuccess {String} cities.name City name
     * @apiSuccess {Object} cities.county County object
     */
    public function index()
    {
        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->get($apiBase . '/cities');
        
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        }
        
        return response()->json(['message' => 'Failed to fetch cities'], $response->status());
    }

    /**
     * @api {get} /api/cities/:id Get City
     * @apiName GetCity
     * @apiGroup CityGroup
     * @apiParam {Number} id City ID
     * @apiSuccess {Number} id City ID
     * @apiSuccess {String} zip City ZIP code
     * @apiSuccess {String} name City name
     * @apiError {String} message Error when not found
     */
    public function show(int $id)
    {
        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->get($apiBase . '/cities/' . $id);
        
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } elseif ($response->status() === 404) {
            return response()->json(['message' => 'City with id not found'], 404);
        }
        
        return response()->json(['message' => 'Failed to fetch city'], $response->status());
    }

    /**
     * @api {post} /api/cities Create City
     * @apiName CreateCity
     * @apiGroup CityGroup
     * @apiParam {String} zip City ZIP code
     * @apiParam {String} name City name
     * @apiParam {Number} county_id County ID
     * @apiSuccess (Created 201) {Number} id Created city ID
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
        ]);

        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->post($apiBase . '/cities', $data);
        
        if ($response->successful()) {
            return response()->json($response->json(), 201);
        } elseif ($response->status() === 422) {
            return response()->json($response->json(), 422);
        }
        
        return response()->json(['message' => 'Failed to create city'], $response->status());
    }

    /**
     * @api {put} /api/cities/:id Update City
     * @apiName UpdateCity
     * @apiGroup CityGroup
     * @apiParam {Number} id City ID
     * @apiParam {String} zip City ZIP code
     * @apiParam {String} name City name
     * @apiParam {Number} county_id County ID
     * @apiSuccess {Object} city Updated city object
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
        ]);

        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->put($apiBase . '/cities/' . $id, $data);
        
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } elseif ($response->status() === 404) {
            return response()->json(['message' => 'City with id not found'], 404);
        }
        
        return response()->json(['message' => 'Failed to update city'], $response->status());
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
        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->delete($apiBase . '/cities/' . $id);
        
        if ($response->status() === 204) {
            return response()->json(null, 204);
        } elseif ($response->status() === 404) {
            return response()->json(['message' => 'City with id not found'], 404);
        }
        
        return response()->json(['message' => 'Failed to delete city'], $response->status());
    }

    /* =====================================================
     | EXPORT METHODS
     |=====================================================*/

    // Controller
    public function export(Request $request)
    {
        $token = session('api_token');
        if (!$token) {
            return redirect()->route('api.login.form')->with('error', 'Please login to API first');
        }

        $apiBase = config('services.api.base_uri');

        $citiesResponse = Http::withToken($token)->get($apiBase . '/cities');
        $citiesData = collect($citiesResponse->json());

        $query = $citiesData;

        // Filters
        if ($search = $request->input('search')) {
            $query = $query->filter(function ($city) use ($search) {
                return str_contains(strtolower($city['name']), strtolower($search)) ||
                       str_contains($city['zip'], $search);
            });
        }

        if ($countyId = $request->input('county_filter')) {
            $query = $query->where('county_id', $countyId);
        }

        if ($letter = $request->input('letter')) {
            $query = $query->filter(function ($city) use ($letter) {
                return str_starts_with(strtolower($city['name']), strtolower($letter));
            });
        }

        $cities = $query->sortBy('name');

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
                fputcsv($handle, [$city['zip'], $city['name'], $city['county']['name']]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPDF($cities)
    {
        // Convert to objects for view compatibility
        $cities = $cities->map(function ($city) {
            $city['county'] = (object) $city['county'];
            return (object) $city;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.cities-pdf', compact('cities'));
        return $pdf->download('cities.pdf');
    }

    // -------------------
    // Web Methods (Blade)
    // -------------------

    // Index page with filters
    public function webIndex(Request $request)
    {
        $apiBase = config('services.api.base_uri');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('api.login.form')->with('error', 'Please login to API first');
        }

        $countiesResponse = Http::withToken($token)->get($apiBase . '/counties');
        $counties = collect($countiesResponse->json())->sortBy('name')->values();

        $citiesResponse = Http::withToken($token)->get($apiBase . '/cities');
        $citiesData = collect($citiesResponse->json());

        $query = $citiesData;

        // Search filter
        if ($search = $request->input('search')) {
            $query = $query->filter(function ($city) use ($search) {
                return str_contains(strtolower($city['name']), strtolower($search)) ||
                       str_contains($city['zip'], $search);
            });
        }

        // County filter
        if ($countyId = $request->input('county_filter')) {
            $query = $query->where('county_id', $countyId);
        }

        // Alphabetical filter
        if ($letter = $request->input('letter')) {
            $query = $query->filter(function ($city) use ($letter) {
                return str_starts_with(strtolower($city['name']), strtolower($letter));
            });
        }

        $cities = $query->sortBy('name')->paginate(25);
        $cities->getCollection()->transform(function ($city) {
            $city['county'] = (object) $city['county'];
            return (object) $city;
        });

        return view('cities-view', [
            'cities' => $cities,
            'counties' => $counties,
        ]);
    }

    // Web Create (from form)
    public function webStore(Request $request)
    {
        $token = session('api_token');
        if (!$token) {
            return redirect()->route('api.login.form')->with('error', 'Please login to API first');
        }

        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->post($apiBase . '/cities', $data);

        if ($response->successful()) {
            return redirect()->route('cities-view.index')->with('message', 'City created successfully');
        } else {
            return back()->withErrors(['error' => 'Failed to create city']);
        }
    }

    // Web Update (from form)
    public function webUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'zip' => 'required|digits:4',
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::put($apiBase . '/cities/' . $id, $data);

        if ($response->successful()) {
            return redirect()->route('cities-view.index')->with('message', 'City updated successfully');
        } else {
            return back()->withErrors(['error' => 'Failed to update city']);
        }
    }

    // Web Destroy
    public function webDestroy($id)
    {
        $apiBase = config('services.api.base_uri');
        $response = Http::delete($apiBase . '/cities/' . $id);

        if ($response->successful()) {
            return redirect()->route('cities-view.index')->with('message', 'City deleted successfully');
        } else {
            return back()->withErrors(['error' => 'Failed to delete city']);
        }
    }
}

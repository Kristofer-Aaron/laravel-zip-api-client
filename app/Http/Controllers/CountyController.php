<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @apiDefine CountyGroup Counties
 * @apiDescription API endpoints for managing counties and their cities.
 */
class CountyController extends Controller
{
    /* =====================================================
     | API METHODS (JSON)
     |=====================================================*/

    /**
     * @api {get} /api/counties List Counties
     * @apiName GetCounties
     * @apiGroup CountyGroup
     * @apiSuccess {Object[]} counties List of counties
     * @apiSuccess {Number} counties.id County ID
     * @apiSuccess {String} counties.name County name
     */
    public function index()
    {
        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->get($apiBase . '/counties');
        
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        }
        
        return response()->json(['message' => 'Failed to fetch counties'], $response->status());
    }

    /**
     * @api {get} /api/counties/:id Get County
     * @apiName GetCounty
     * @apiGroup CountyGroup
     * @apiParam {Number} id County ID
     * @apiSuccess {Number} id County ID
     * @apiSuccess {String} name County name
     * @apiError {String} message Error when not found
     */
    public function show(int $id)
    {
        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->get($apiBase . '/counties/' . $id);
        
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } elseif ($response->status() === 404) {
            return response()->json(['message' => 'County with id not found'], 404);
        }
        
        return response()->json(['message' => 'Failed to fetch county'], $response->status());
    }

    /**
     * @api {post} /api/counties Create County
     * @apiName CreateCounty
     * @apiGroup CountyGroup
     * @apiParam {String} name County name (unique)
     * @apiSuccess (Created 201) {Number} id Created county ID
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->post($apiBase . '/counties', $data);
        
        if ($response->successful()) {
            return response()->json($response->json(), 201);
        } elseif ($response->status() === 422) {
            return response()->json($response->json(), 422);
        }
        
        return response()->json(['message' => 'Failed to create county'], $response->status());
    }

    /**
     * @api {put} /api/counties/:id Update County
     * @apiName UpdateCounty
     * @apiGroup CountyGroup
     * @apiParam {Number} id County ID
     * @apiParam {String} name County name
     * @apiSuccess {Object} county Updated county object
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->put($apiBase . '/counties/' . $id, $data);
        
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } elseif ($response->status() === 404) {
            return response()->json(['message' => 'County with id not found'], 404);
        }
        
        return response()->json(['message' => 'Failed to update county'], $response->status());
    }

    /**
     * @api {delete} /api/counties/:id Delete County
     * @apiName DeleteCounty
     * @apiGroup CountyGroup
     * @apiParam {Number} id County ID
     * @apiSuccess (No Content 204) - No response body
     */
    public function destroy(int $id)
    {
        $token = auth('sanctum')->token();
        $apiBase = config('services.api.base_uri');
        
        $response = Http::withToken($token)->delete($apiBase . '/counties/' . $id);
        
        if ($response->status() === 204) {
            return response()->json(null, 204);
        } elseif ($response->status() === 404) {
            return response()->json(['message' => 'County with id not found'], 404);
        }
        
        return response()->json(['message' => 'Failed to delete county'], $response->status());
    }

    // Export Counties
    public function export(Request $request)
    {
        $apiBase = config('services.api.base_uri');
        $response = Http::get($apiBase . '/counties');
        $countiesData = collect($response->json());

        $counties = $countiesData->map(function ($county) {
            $county['cities_count'] = 0; // Placeholder
            return (object) $county;
        })->sortBy('name');

        $type = $request->input('type', 'csv'); // default CSV

        if ($type === 'pdf') {
            return $this->exportCountiesPDF($counties);
        } else {
            return $this->exportCountiesCSV($counties);
        }
    }

    // CSV
    private function exportCountiesCSV($counties)
    {
        $filename = 'counties.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($counties) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['County', 'Cities Count']);
            foreach ($counties as $county) {
                fputcsv($handle, [$county->name, $county->cities_count]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // PDF
    private function exportCountiesPDF($counties)
    {
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.counties-pdf', compact('counties'))
            ->download('counties.pdf');
    }

    
    /* =====================================================
     | WEB METHODS (BLADE VIEWS)
     |=====================================================*/

    /**
     * GET /counties
     */
    public function webIndex()
    {
        $token = session('api_token');
        if (!$token) {
            return redirect()->route('api.login.form')->with('error', 'Please login to API first');
        }

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->get($apiBase . '/counties');
        $countiesData = collect($response->json());

        // Add cities_count (assuming API doesn't provide it, or fetch cities and count)
        // For simplicity, set to 0 or fetch separately
        $counties = $countiesData->map(function ($county) {
            $county['cities_count'] = 0; // Placeholder
            return (object) $county;
        })->sortBy('name')->paginate(25);

        return view('counties-view', [
            'counties' => $counties,
        ]);
    }

    /**
     * POST /counties
     */
    public function webStore(Request $request)
    {
        $token = session('api_token');
        if (!$token) {
            return redirect()->route('api.login.form')->with('error', 'Please login to API first');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::withToken($token)->post($apiBase . '/counties', $data);

        if ($response->successful()) {
            return redirect()
                ->route('counties-view.index')
                ->with('message', 'County created successfully.');
        } else {
            return back()->withErrors(['error' => 'Failed to create county']);
        }
    }

    /**
     * PUT /counties/{id}
     */
    public function webUpdate(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $apiBase = config('services.api.base_uri');
        $response = Http::put($apiBase . '/counties/' . $id, $data);

        if ($response->successful()) {
            return redirect()
                ->route('counties-view.index')
                ->with('message', 'County updated successfully.');
        } else {
            return back()->withErrors(['error' => 'Failed to update county']);
        }
    }

    /**
     * DELETE /counties/{id}
     */
    public function webDestroy(int $id)
    {
        $apiBase = config('services.api.base_uri');
        $response = Http::delete($apiBase . '/counties/' . $id);

        if ($response->successful()) {
            return redirect()
                ->route('counties-view.index')
                ->with('message', 'County deleted successfully.');
        } else {
            return back()->withErrors(['error' => 'Failed to delete county']);
        }
    }
}

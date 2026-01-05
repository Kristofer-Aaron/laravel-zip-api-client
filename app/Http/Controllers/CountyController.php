<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\County;

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
        return response()->json(County::all(), 200);
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
        $county = County::find($id);

        if (!$county) {
            return response()->json(['message' => 'County with id not found'], 404);
        }

        return response()->json($county, 200);
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
            'name' => 'required|string|max:255|unique:counties,name',
        ]);

        $county = County::create($data);

        return response()->json($county, 201);
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
        $county = County::find($id);

        if (!$county) {
            return response()->json(['message' => 'County with id not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $county->update($data);

        return response()->json($county, 200);
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
        $county = County::find($id);

        if (!$county) {
            return response()->json(['message' => 'County with id not found'], 404);
        }

        $county->delete();

        return response()->json(null, 204);
    }

    // Export Counties
    public function export(Request $request)
    {
        $counties = County::withCount('cities')->orderBy('name')->get();

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
        $counties = County::withCount('cities')
            ->orderBy('name')
            ->paginate(25);

        return view('counties-view', [
            'counties' => $counties,
        ]);
    }

    /**
     * POST /counties
     */
    public function webStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:counties,name',
        ]);

        County::create($data);

        return redirect()
            ->route('counties-view.index')
            ->with('message', 'County created successfully.');
    }

    /**
     * PUT /counties/{id}
     */
    public function webUpdate(Request $request, int $id)
    {
        $county = County::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:counties,name,' . $id,
        ]);

        $county->update($data);

        return redirect()
            ->route('counties-view.index')
            ->with('message', 'County updated successfully.');
    }

    /**
     * DELETE /counties/{id}
     */
    public function webDestroy(int $id)
    {
        $county = County::findOrFail($id);
        $county->delete();

        return redirect()
            ->route('counties-view.index')
            ->with('message', 'County deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\County;

class CountyController extends Controller
{
    /* =====================================================
     | API METHODS (JSON)
     |=====================================================*/

    /**
     * GET /api/counties
     */
    public function index()
    {
        return response()->json(County::all(), 200);
    }

    /**
     * GET /api/counties/{id}
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
     * POST /api/counties
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
     * PUT /api/counties/{id}
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
     * DELETE /api/counties/{id}
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

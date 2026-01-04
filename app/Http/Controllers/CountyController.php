<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\County;

class CountyController extends Controller
{
    /**
     * @api {get} /counties Get all counties
     * @apiName GetCounties
     * @apiGroup County
     * @apiDescription Returns all counties.
     *
     * @apiSuccess (200) {Object[]} counties List of counties
     * @apiSuccess (200) {Number} counties.id County ID
     * @apiSuccess (200) {String} counties.name County name
     */
    public function index()
    {
        $counties = County::get();
        return response()->json($counties, 200);
    }

    /**
     * @api {get} /counties/:id Get a single county
     * @apiName GetCounty
     * @apiGroup County
     * @apiDescription Returns a county by its ID.
     *
     * @apiParam {Number} id County ID
     *
     * @apiSuccess (200) {Number} id County ID
     * @apiSuccess (200) {String} name County name
     *
     * @apiError (404) CountyNotFound County with id not found
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
     * @api {post} /counties Create a new county
     * @apiName CreateCounty
     * @apiGroup County
     * @apiDescription Creates a new county.
     *
     * @apiBody {String} name County name
     *
     * @apiSuccess (201) {Number} id County ID
     * @apiSuccess (201) {String} name County name
     *
     * @apiError (422) ValidationError Returned if validation fails
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:counties,name',
        ]);

        $county = County::create([
            'name' => $data['name'],
        ]);

        return response()->json($county, 201);
    }

    /**
     * @api {put} /counties/:id Update a county
     * @apiName UpdateCounty
     * @apiGroup County
     * @apiDescription Updates an existing county.
     *
     * @apiParam {Number} id County ID
     *
     * @apiBody {String} name County name
     *
     * @apiSuccess (200) {Number} id County ID
     * @apiSuccess (200) {String} name County name
     *
     * @apiError (404) CountyNotFound County with id not found
     * @apiError (422) ValidationError Returned if validation fails
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
        
        $county->update([
            'name' => $data['name'], 
        ]);
        
        return response()->json($county, 200);
    }

    /**
     * @api {delete} /counties/:id Delete a county
     * @apiName DeleteCounty
     * @apiGroup County
     * @apiDescription Deletes a county by ID.
     *
     * @apiParam {Number} id County ID
     *
     * @apiSuccess (204) NoContent County deleted successfully
     *
     * @apiError (404) CountyNotFound County with id not found
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
}
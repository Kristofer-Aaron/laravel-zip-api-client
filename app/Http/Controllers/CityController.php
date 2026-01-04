<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\County;

class CityController extends Controller
{
    /**
     * @api {get} /cities Get all cities
     * @apiName GetCities
     * @apiGroup City
     * @apiDescription Returns all cities with their associated county.
     *
     * @apiSuccess (200) {Object[]} cities List of cities
     * @apiSuccess (200) {Number} cities.id City ID
     * @apiSuccess (200) {String} cities.zip ZIP code
     * @apiSuccess (200) {String} cities.name City name
     * @apiSuccess (200) {Object} cities.county Related county
     * @apiSuccess (200) {Number} cities.county.id County ID
     * @apiSuccess (200) {String} cities.county.name County name
     */
    public function index()
    {
        $cities = City::with('county')->get();
        return response()->json($cities, 200);
    }

    /**
     * @api {get} /cities/:id Get a single city
     * @apiName GetCity
     * @apiGroup City
     * @apiDescription Returns a single city by ID.
     *
     * @apiParam {Number} id City ID
     *
     * @apiSuccess (200) {Number} id City ID
     * @apiSuccess (200) {String} zip ZIP code
     * @apiSuccess (200) {String} name City name
     * @apiSuccess (200) {Object} county Related county
     *
     * @apiError (404) CityNotFound City with id not found
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
     * @api {post} /cities Create a new city
     * @apiName CreateCity
     * @apiGroup City
     * @apiDescription Creates a new city. If the specified county does not exist, it will be created.
     *
     * @apiBody {String{4}} zip ZIP code (4 digits)
     * @apiBody {String} name City name
     * @apiBody {String} county County name
     *
     * @apiSuccess (201) {Number} id City ID
     * @apiSuccess (201) {String} zip ZIP code
     * @apiSuccess (201) {String} name City name
     * @apiSuccess (201) {Object} county Related county
     *
     * @apiError (422) ValidationError Returned if request validation fails
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
     * @api {put} /cities/:id Update an existing city
     * @apiName UpdateCity
     * @apiGroup City
     * @apiDescription Updates the city with the given ID. Creates the county if it does not exist.
     *
     * @apiParam {Number} id City ID
     *
     * @apiBody {String{4}} zip ZIP code (4 digits)
     * @apiBody {String} name City name
     * @apiBody {String} county County name
     *
     * @apiSuccess (200) {Number} id City ID
     * @apiSuccess (200) {String} zip ZIP code
     * @apiSuccess (200) {String} name City name
     * @apiSuccess (200) {Object} county Related county
     *
     * @apiError (404) CityNotFound City with id not found
     * @apiError (422) ValidationError Returned if request validation fails
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
     * @api {delete} /cities/:id Delete a city
     * @apiName DeleteCity
     * @apiGroup City
     * @apiDescription Deletes a city by ID.
     *
     * @apiParam {Number} id City ID
     *
     * @apiSuccess (204) NoContent City deleted successfully
     *
     * @apiError (404) CityNotFound City with id not found
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
}

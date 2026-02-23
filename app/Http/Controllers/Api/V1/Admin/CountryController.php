<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Country::orderBy('name', 'asc');

        if ($request->has('all')) {
            return response()->json([
                'data' => $query->get()
            ]);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 10))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:countries,name',
            'code' => 'required|string|max:5|unique:countries,code',
            'status' => 'nullable|boolean',
        ]);

        $country = Country::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'status' => $request->get('status', true),
        ]);

        return response()->json([
            'message' => 'Country created successfully',
            'data' => $country
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        return response()->json([
            'data' => $country
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|unique:countries,name,' . $country->id,
            'code' => 'required|string|max:5|unique:countries,code,' . $country->id,
            'status' => 'nullable|boolean',
        ]);

        $country->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'status' => $request->get('status', $country->status),
        ]);

        return response()->json([
            'message' => 'Country updated successfully',
            'data' => $country
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json([
            'message' => 'Country deleted successfully'
        ]);
    }
}

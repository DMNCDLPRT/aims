<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('location/index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $validatedData = $request->validated();

        $location = Location::create($validatedData);

        return response()->json([
            'message' => 'Location created successfully!',
            'location' => $location // Optionally return the created category data
        ], 201); // 201 Created status code
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location = Location::findOrFail($location->id);

        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        return response()->json(['location' => $location], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            $location->update($validatedData);

            DB::commit();

            return response()->json([
                'message' => 'Location updated successfully!',
                'location' => $location->fresh()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'location' => $location->fresh()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();

            return response()->json(['message' => 'Location deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete location.'], 500);
        }
    }
}

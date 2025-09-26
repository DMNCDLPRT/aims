<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManufacturereRequest;
use App\Http\Requests\UpdateManufacturereRequest;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('manufacturer/index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManufacturereRequest $request)
    {
        $validatedData = $request->validated();

        $manufacturer = Manufacturer::create($validatedData);

        return response()->json([
            'message' => 'Manufacturer created successfully!',
            'manufacturer' => $manufacturer // Optionally return the created category data
        ], 201); // 201 Created status code
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacturer $manufacturer)
    {
        $manufacturer = Manufacturer::findOrFail($manufacturer->id);

        if (!$manufacturer) {
            return response()->json(['message' => 'Manufacturer not found'], 404);
        }

        return response()->json($manufacturer, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManufacturereRequest $request, Manufacturer $manufacturer)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            $manufacturer->update($validatedData);

            DB::commit();

            return response()->json([
                'message' => 'Manufacturer updated successfully!',
                'manufacturer' => $manufacturer->fresh()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'manufacturer' => $manufacturer->fresh()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $manufacturer = Manufacturer::findOrFail($id);
            $manufacturer->delete();

            return response()->json(['message' => 'Manufacturer deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete manufacturer.'], 500);
        }
    }

    public function list(Request $request)
    {
        $query = Manufacturer::query();

        if ($request->has('searchtext') && !empty($request->input('searchtext'))) {
            $search = $request->input('searchtext');
            $query
                ->whereLike('name', '%' . $search . '%')
                ->orWhereLike('url', '%' . $search . '%')
                ->orWhereLike('support_url', '%' . $search . '%')
                ->orWhereLike('support_phone', '%' . $search . '%')
                ->orWhereLike('support_email', '%' . $search . '%');
        }

        if ($request->has('sort_field') && $request->has('sort_direction')) {
            $query->orderBy($request->input('sort_field'), $request->input('sort_direction'));
        } else {
            $query->orderBy('name', 'asc'); // Default sorting
        }

        $manufacturers = ManufacturerResource::collection(
            $query->orderBy('name', 'asc')->paginate($request->input('per_page', 5))
        );

        return $manufacturers;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetsResource;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get(['id', 'name']);
        $locations = Location::orderBy('name', 'asc')->get(['id', 'name']);
        $manufacturers = Manufacturer::orderBy('name', 'asc')->get(['id', 'name']);
        $users = User::orderBy('name', 'asc')->get(['id', 'name']);

        return inertia('asset/index', [
            'categories' => $categories,
            'locations' => $locations,
            'manufacturers' => $manufacturers,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetRequest $request)
    {
        $validatedData = $request->validated();

        $asset = Asset::create($validatedData);

        return response()->json([
            'message' => 'Asset created successfully!',
            'asset' => $asset
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset = Asset::with(['category', 'location', 'manufacturer', 'assignedToUser'])
            ->findOrFail($asset->id);


        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        return response()->json($asset, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            $asset->update($validatedData);

            DB::commit();

            return response()->json([
                'message' => 'Asset updated successfully!',
                'asset' => $asset->fresh()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'asset' => $asset->fresh()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($asset)
    {
        try {
            $asset = Asset::findOrfail($asset->id);
            $asset->destroy();
        } catch (\Exception $e) {
            Log::error('error: ' . $e->getMessage());
            return response()->json(['message', 'Asset deleted successfully']);
        }
    }

    public function list(Request $request)
    {
        $query = Asset::query();
        $query->with([
            'category',
            'manufacturer',
            'location',
            'assignedToUser'
        ]);

        if ($request->filled('searchtext')) {
            $search = $request->input('searchtext');

            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('model_name', 'like', "%{$search}%")
                    ->orWhere('purchase_date', 'like', "%{$search}%")
                    ->orWhere('purchase_price', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    // Related models
                    ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('manufacturer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('location', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('assignedToUser', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->has('sort_field') && $request->has('sort_direction')) {
            $query->orderBy($request->input('sort_field'), $request->input('sort_direction'));
        } else {
            $query->orderBy('name', 'asc'); // Default sorting
        }

        $assets = AssetsResource::collection(
            $query->paginate($request->input('per_page', 5))
        );

        return $assets;
    }
}

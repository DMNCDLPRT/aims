<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
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
        return inertia('asset/index');
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
        $asset = Asset::findOrFail($asset->id);

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        return response()->json(['asset' => $asset], 200);
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
}

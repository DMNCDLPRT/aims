<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia('Dashboard');
    }

    public function stats() {
        $totalAssets = Asset::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalManufacturers = Manufacturer::count();
        $totalLocations = Location::count();

        $assetsByStatus = Asset::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $assetsByCategory = Asset::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();

        $assetsByLocation = Asset::select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->with('location:id,name')
            ->get();

        $assetsByAssignedUser = Asset::select('assigned_to_user_id', DB::raw('count(*) as total'))
            ->groupBy('assigned_to_user_id')
            ->with('assignedToUser:id,name')
            ->get();

        return response()->json([
            'totals' => [
                'total_assets' => $totalAssets,
                'total_categories' => $totalCategories,
                'total_users' => $totalUsers,
                'total_manufacturers' => $totalManufacturers,
                'total_locations' => $totalLocations,
            ],
            'charts' => [
                'assets_by_status' => $assetsByStatus,
                'assets_by_category' => $assetsByCategory,
                'assets_by_location' => $assetsByLocation,
                'assets_by_assigned_user' => $assetsByAssignedUser,
            ],
        ], 200);
    }
}

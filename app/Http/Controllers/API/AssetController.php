<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display assets list
     */
    public function index(Request $request)
    {
        $query = Asset::with('category');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('assigned_to', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query->latest()->paginate(
                $request->get('per_page', 10)
            )
        );
    }

    /**
     * Store new asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',

            'category_id' => 'required|exists:asset_categories,id',

            'status' => 'required|in:Available,Assigned,Maintenance,Disposed',

            'asset_tag' => 'required|string|max:255|unique:assets,asset_tag',

            'serial_number' => 'nullable|string|max:255',

            'emp_id' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',

            'approved_by' => 'nullable|string|max:255',
            'purchased_by' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',

            'remark' => 'nullable|string',
        ]);

        /*
         * Standardize values
         */
        $validated['asset_tag'] =
            strtoupper(trim($validated['asset_tag']));

        if (!empty($validated['serial_number'])) {
            $validated['serial_number'] =
                strtoupper(trim($validated['serial_number']));
        }

        $asset = Asset::create($validated);

        return response()->json([
            'message' => 'Asset created successfully',
            'data' => Asset::with('category')
                ->find($asset->id),
        ], 201);
    }

    /**
     * Display asset details
     */
    public function show($id)
    {
        $asset = Asset::with('category')
            ->findOrFail($id);

        return response()->json($asset);
    }

    /**
     * Update asset
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',

            'category_id' => 'required|exists:asset_categories,id',

            'status' => 'required|in:Available,Assigned,Maintenance,Disposed',

            'asset_tag' =>
                'required|string|max:255|unique:assets,asset_tag,' . $id,

            'serial_number' => 'nullable|string|max:255',

            'emp_id' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',

            'approved_by' => 'nullable|string|max:255',
            'purchased_by' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',

            'remark' => 'nullable|string',
        ]);

        /*
         * Standardize values
         */
        $validated['asset_tag'] =
            strtoupper(trim($validated['asset_tag']));

        if (!empty($validated['serial_number'])) {
            $validated['serial_number'] =
                strtoupper(trim($validated['serial_number']));
        }

        $asset->update($validated);

        return response()->json([
            'message' => 'Asset updated',
            'data' => Asset::with('category')
                ->find($asset->id),
        ]);
    }

    /**
     * Delete asset
     */
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        $asset->delete();

        return response()->json([
            'message' => 'Asset deleted',
        ]);
    }
}
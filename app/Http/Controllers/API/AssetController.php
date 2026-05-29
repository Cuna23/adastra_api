<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return response()->json(
            Asset::with('category')
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'category_id' => 'required|exists:asset_categories,id',
            'status' => 'required|string',
            'asset_tag' => 'required|string|unique:assets,asset_tag',
            'serial_number' => 'nullable|string',
            'emp_id' => 'nullable|string',
            'department' => 'nullable|string',
            'approved_by' => 'nullable|string',
            'purchased_by' => 'nullable|string',
            'assigned_to' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        $asset = Asset::create($validated);

        return response()->json([
            'message' => 'Asset created successfully',
            'data' => $asset,
        ], 201);
    }

    public function show($id)
    {
        $asset = Asset::with('category')
            ->findOrFail($id);

        return response()->json($asset);
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'category_id' => 'required|exists:asset_categories,id',
            'status' => 'required|string',
            'asset_tag' => 'required|string|unique:assets,asset_tag,' . $id,
            'serial_number' => 'nullable|string',
            'emp_id' => 'nullable|string',
            'department' => 'nullable|string',
            'approved_by' => 'nullable|string',
            'purchased_by' => 'nullable|string',
            'assigned_to' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        $asset->update($validated);

        return response()->json([
            'message' => 'Asset updated',
            'data' => $asset,
        ]);
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        $asset->delete();

        return response()->json([
            'message' => 'Asset deleted',
        ]);
    }
}
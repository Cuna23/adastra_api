<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    /**
     * Standardize incoming asset fields
     */
    private function standardize(array $data): array
    {
        // UPPERCASE — identifiers
        foreach (['asset_tag', 'serial_number', 'emp_id'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = strtoupper(trim($data[$field]));
            } 
        }

        // Title Case — names & departments
        foreach (['brand', 'model', 'assigned_to', 'approved_by', 'purchased_by', 'department'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = Str::title(trim($data[$field]));
            }
        }

        // Sentence case — free text
        if (!empty($data['remark'])) {
            $data['remark'] = ucfirst(strtolower(trim($data['remark'])));
        }

        return $data;
    }

    /**
     * Display assets list
     */
    public function index(Request $request)
    {
        $query = Asset::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

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
            $query->latest()->paginate($request->get('per_page', 30))
        );
    }

    /**
     * Store new asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand'         => 'nullable|string|max:255',
            'model'         => 'nullable|string|max:255',
            'category_id'   => 'required|exists:asset_categories,id',
            'status'        => 'required|in:Pending,Available,In Process,Resolved,Maintenance,Disposed',
            'asset_tag'     => 'required|string|max:255|unique:assets,asset_tag',
            'serial_number' => 'nullable|string|max:255',
            'emp_id'        => 'nullable|string|max:255',
            'department'    => 'nullable|string|max:255',
            'approved_by'   => 'nullable|string|max:255',
            'purchased_by'  => 'nullable|string|max:255',
            'assigned_to'   => 'nullable|string|max:255',
            'remark'        => 'nullable|string',
        ]);

        $validated = $this->standardize($validated);

        $asset = Asset::create($validated);

        return response()->json([
            'message' => 'Asset created successfully',
            'data'    => Asset::with('category')->find($asset->id),
        ], 201);
    }

    /**
     * Display asset details
     */
    public function show($id)
    {
        return response()->json(
            Asset::with('category')->findOrFail($id)
        );
    }

    /**
     * Update asset
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'brand'         => 'nullable|string|max:255',
            'model'         => 'nullable|string|max:255',
            'category_id'   => 'required|exists:asset_categories,id',
            'status'        => 'required|in:Pending,Available,In Process,Resolved,Maintenance,Disposed',
            'asset_tag'     => 'required|string|max:255|unique:assets,asset_tag,' . $id,
            'serial_number' => 'nullable|string|max:255',
            'emp_id'        => 'nullable|string|max:255',
            'department'    => 'nullable|string|max:255',
            'approved_by'   => 'nullable|string|max:255',
            'purchased_by'  => 'nullable|string|max:255',
            'assigned_to'   => 'nullable|string|max:255',
            'remark'        => 'nullable|string',
        ]);

        $validated = $this->standardize($validated);

        $asset->update($validated);

        return response()->json([
            'message' => 'Asset updated',
            'data'    => Asset::with('category')->find($asset->id),
        ]);
    }

    /**
     * Delete asset
     */
    public function destroy($id)
    {
        Asset::findOrFail($id)->delete();

        return response()->json(['message' => 'Asset deleted']);
    }
}
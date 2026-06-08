<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        return response()->json(
            AssetCategory::latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name',
        ]);

        $category = AssetCategory::create($validated);

        return response()->json([
            'message' => 'Category created',
            'id'      => $category->id,
            'name'    => $category->name,
        ], 201);
    }

    public function show($id)
    {
        $category = AssetCategory::findOrFail($id);

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = AssetCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:asset_categories,name,' . $id,
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated',
            'data' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = AssetCategory::findOrFail($id);

        if ($category->assets()->exists()) {
            return response()->json([
                'message' => 'Category is being used'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}
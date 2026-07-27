<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Upload: admin & super_admin
    private function authorizeUploader()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }
    }

    // Delete: super_admin only
    private function authorizeSuperAdmin()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }
    }

    // GET /org-chart — everyone can view, returns the latest one
    public function orgChart()
    {
        $chart = Company::where('type', 'org_chart')->latest()->first();
        return response()->json($chart);
    }

    // GET /floor-maps — everyone can view, list all floors ordered
    public function floorMaps()
    {
        $floors = Company::where('type', 'floor_map')->orderBy('sort_order')->get();
        return response()->json($floors);
    }

    // POST /org-chart — admin/super_admin upload new chart
    public function storeOrgChart(Request $request)
    {
        $this->authorizeUploader();

        $request->validate(['image' => 'required|image|max:10240']);

        $path = $request->file('image')->store('org-charts', 'public');

        $chart = Company::create([
            'type' => 'org_chart',
            'image_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Org chart uploaded successfully', 'data' => $chart], 201);
    }

    // POST /floor-maps — admin/super_admin create new floor
    public function storeFloorMap(Request $request)
    {
        $this->authorizeUploader();

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:10240',
            'sort_order' => 'nullable|integer',
        ]);

        $path = $request->file('image')->store('floor-maps', 'public');

        $floor = Company::create([
            'type' => 'floor_map',
            'title' => $request->title,
            'image_path' => $path,
            'sort_order' => $request->sort_order ?? 0,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Floor map created successfully', 'data' => $floor], 201);
    }

    // DELETE /company/{id} — super_admin only (applies to org_chart or floor_map)
    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        Company::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
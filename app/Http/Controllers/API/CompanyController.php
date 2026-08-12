<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Upload/edit: admin & super_admin
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

    // GET /org-chart
    // public function orgChart()
    // {
    //     $chart = Company::where('type', 'org_chart')->latest()->first();
    //     return response()->json($chart);
    // }

    // GET /floor-maps
    public function floorMaps()
    {
        $floors = Company::where('type', 'floor_map')->orderBy('sort_order')->get();
        return response()->json($floors);
    }

    // GET /about
    public function about()
    {
        $about = Company::where('type', 'about')->latest()->first();
        return response()->json($about);
    }

    // GET /vision-mission
    public function visionMission()
    {
        $vm = Company::where('type', 'vision_mission')->latest()->first();
        return response()->json($vm);
    }

    // POST /org-chart
    // public function storeOrgChart(Request $request)
    // {
    //     $this->authorizeUploader();

    //     $request->validate([
    //         'image' => 'required|image|max:10240',
    //         'title' => 'nullable|string|max:255',
    //     ]);

    //     $path = $request->file('image')->store('org-charts', 'public');

    //     $chart = Company::create([
    //         'type' => 'org_chart',
    //         'title' => $request->title ?? 'Company structure',
    //         'image_path' => $path,
    //         'uploaded_by' => auth()->id(),
    //     ]);

    //     return response()->json(['message' => 'Org chart uploaded successfully', 'data' => $chart], 201);
    // }

    // POST /floor-maps
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

    // PATCH /company/{id}/title — org chart title OR floor map title
    public function updateTitle(Request $request, string $id)
    {
        $this->authorizeUploader();

        $request->validate(['title' => 'required|string|max:255']);

        $item = Company::findOrFail($id);
        $item->update(['title' => $request->title]);

        return response()->json(['message' => 'Title updated successfully', 'data' => $item]);
    }

    // POST /company/content — upsert About / Vision & Mission
    // body: { type: 'about', content: '...' }
    // body: { type: 'vision_mission', vision: '...', mission: '...' }
    public function upsertContent(Request $request)
    {
        $this->authorizeUploader();

        $request->validate(['type' => 'required|in:about,vision_mission']);

        if ($request->type === 'about') {
            $request->validate(['content' => 'required|string']);
            $content = $request->content;
        } else {
            $request->validate([
                'vision' => 'required|string',
                'mission' => 'required|string',
            ]);
            $content = json_encode([
                'vision' => $request->vision,
                'mission' => $request->mission,
            ]);
        }

        $item = Company::updateOrCreate(
            ['type' => $request->type],
            ['content' => $content, 'uploaded_by' => auth()->id()]
        );

        return response()->json(['message' => 'Saved successfully', 'data' => $item]);
    }

    // DELETE /company/{id} — super_admin only (org_chart or floor_map)
    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        Company::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
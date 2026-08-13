<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary as CloudinarySDK;

class TeamMemberController extends Controller
{
    private function authorizeUploader()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }
    }

    private function authorizeSuperAdmin()
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(response()->json(['message' => 'Unauthorized'], 403));
        }
    }

    public function index()
    {
        $members = TeamMember::with('department')
            ->orderBy('department_id')
            ->orderBy('sort_order')
            ->get();

        return response()->json($members);
    }

    public function store(Request $request)
    {
        $this->authorizeUploader();

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'background' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
            'sort_order' => 'nullable|integer',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $cloudinary = new CloudinarySDK(env('CLOUDINARY_URL'));
            $uploaded = $cloudinary->uploadApi()->upload($request->file('photo')->getRealPath(), [
                'folder' => 'team-members',
            ]);
            $photoUrl = $uploaded['secure_url'];
        }

        $member = TeamMember::create([
            'name' => $request->name,
            'position' => $request->position,
            'department_id' => $request->department_id,
            'background' => $request->background,
            'photo_path' => $photoUrl,
            'sort_order' => $request->sort_order ?? 0,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Team member added successfully', 'data' => $member->load('department')], 201);
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeUploader();

        $member = TeamMember::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'background' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
            'sort_order' => 'nullable|integer',
        ]);

        $photoUrl = $member->photo_path;
        if ($request->hasFile('photo')) {
            $cloudinary = new CloudinarySDK(env('CLOUDINARY_URL'));
            $uploaded = $cloudinary->uploadApi()->upload($request->file('photo')->getRealPath(), [
                'folder' => 'team-members',
            ]);
            $photoUrl = $uploaded['secure_url'];
        }

        $member->update([
            'name' => $request->name,
            'position' => $request->position,
            'department_id' => $request->department_id,
            'background' => $request->background,
            'photo_path' => $photoUrl,
            'sort_order' => $request->sort_order ?? $member->sort_order,
        ]);

        return response()->json(['message' => 'Team member updated successfully', 'data' => $member->load('department')]);
    }

    public function destroy(string $id)
    {
        $this->authorizeSuperAdmin();

        TeamMember::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}